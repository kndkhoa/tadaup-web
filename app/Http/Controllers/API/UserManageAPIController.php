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

            $user = null;
            DB::transaction(function () use ($request) {
                $user = User::create([
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
                        'customer_id' => $user->user_id,
                        'user_id' => $user->user_id,
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
                'customerID' => 'required|integer|max:250',
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
}
