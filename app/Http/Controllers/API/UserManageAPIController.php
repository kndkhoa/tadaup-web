<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampainFX_Txn;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserManageAPIController extends Controller
{
    
    public function register(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'telegramid' => 'required|string|max:250',
                'name' => 'required|string|max:250',
                'email' => 'required|email|max:250|unique:users',
                'password' => 'required|min:8',
                'fullname' => 'required|string|max:250',
                'phone' => 'required|string|max:250',
                'ewalletAddress' => 'required|string|max:250',
                'image_font' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                'image_back' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //check exists user_sponser_id
            if($request->sponser_userid){
                $customer = Customer::find($request->sponser_userid);
                if(!$customer)
                {
                    return response()->json(['error' => 'Sponser Userid not found.'], 400);
                }
            }

            DB::transaction(function () use ($request) {
                $user = User::create([
                    'user_id' => $request->telegramid,
                    'username' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'level' => 1,
                    'status' => 'ACT'
                ]);
                try {
                    $path1 = $request->file('image_font')->store('dist/img/CCCD', 'public');
                    $path2 = $request->file('image_back')->store('dist/img/CCCD', 'public');
                    Customer::create([
                        'customer_id' => $request->telegramid,
                        'user_id' => $request->telegramid,
                        'user_sponser_id' => $request->sponser_userid,
                        'full_name' => $request->fullname,
                        'phone' => $request->phone,
                        'image_font_id' => $path1,
                        'image_back_id' => $path2,
                        'ewalletAddress' => $request->ewalletAddress,
                        'ewalletNetwork' => 'TON',
                    ]);
                } 
                catch (QueryException $e) 
                {
                    Log::error('Error creating customer: ' . $e->getMessage());
                    return response()->json(['error' => 'Error creating customer: ' . $e->getMessage()], 400);
                }
           
            });
            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);

            return response()->json(['user' => $request->name, 'message' => 'Create user successfully!'], 201);
        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage());
            return response()->json(['error' => 'Create user failed.'], 500);
        }
    }

    public function showCustomerDetail(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'customerID' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //check exists user_sponser_id
            if($request->customerID){
                $customer = Customer::find($request->customerID);
                if(!$customer)
                {
                    return response()->json(['error' => 'Customer ID not found.'], 400);
                }

                //Tree view
                $userTree = Customer::with('children')->find($request->customerID);
                if ($userTree) {
                    $tree = $this->buildTree($userTree);

                    return response()->json(['customer' => $customer, 'MLM'=> $tree, 'message' => 'Get user successfully!'], 201);
                }
                return response()->json(['customer' => $customer, 'MLM'=> [], 'message' => 'Get user successfully!'], 201);
            }

            
        } catch (\Exception $e) {
            Log::error('Get user failed: ' . $e->getMessage());
            return response()->json(['error' => 'Get user failed.'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:250',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //check exists user_sponser_id
            if($request->customerID){
                $customer = Customer::find($request->customerID);
                if(!$customer)
                {
                    return response()->json(['error' => 'Customer ID not found.'], 400);
                }
                return response()->json(['customer' => $customer, 'message' => 'Get user successfully!'], 201);
            }

            
        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage());
            return response()->json(['error' => 'Create user failed.'], 500);
        }
    }

    private function buildTree($user)
    {
        $tree = [
            'id' => $user->user_id,
            'text' => $user->full_name,
            'children' => []
        ];

        foreach ($user->children as $child) {
            $tree['children'][] = $this->buildTree($child);
        }

        return $tree;
    }
}
