<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampainFX_Txn;
use App\Models\CampainFX;
use App\Models\Customer;
use App\Models\CustomerItem;
use App\Models\CustomerConnection;
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
                'telegramid' => 'required|string',
                //'name' => 'required|string|max:250',
                'email' => 'required|email|max:250|unique:users',
                //'password' => 'required|min:8',
                //'fullname' => 'required|string|max:250',
                //'phone' => 'required|string|max:250',
                //'ewalletAddress' => 'required|string|max:250',
                //'image_font' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                //'image_back' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
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
                //create user table
                $user = User::create([
                    'user_id' => $request->telegramid,
                    'username' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make('12345678'),
                    'level' => 1,
                    'status' => 'ACT'
                ]);
                try {

                    //Create customer table
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

                //create customer_item
                $items = [[
                    'customer_id' => $request->telegramid,
                    'type' => '1',
                    'value' =>  '0'
                ],
                [
                    'customer_id' => $request->telegramid,
                    'type' => '2',
                    'value' =>  '0'
                ],
                [
                    'customer_id' => $request->telegramid,
                    'type' => '3',
                    'value' =>  '0'
                ],
                [
                    'customer_id' => $request->telegramid,
                    'type' => '4',
                    'value' =>  '0'
                ]];
                foreach ($items as $item) {
                    CustomerItem::create($item);
                }
            });
            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);

            return response()->json(['user' => $request->email, 'message' => 'Create user successfully!'], 201);
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
                $tree = null;
                $userTree = Customer::with('children')->find($request->customerID);
                if (!$userTree) {
                    $tree = [];
                }
                else{
                    $tree = $this->buildTree($userTree);
                }
            
                //Customer item
                $customerItem = CustomerItem::where('customer_item.customer_id', $request->customerID)
                                ->join('item', 'customer_item.type', '=', 'item.id')
                                ->select('item.item as item','customer_item.value') 
                                ->get();

                //CampaignFX
                $CampainFXTXN_ID = CampainFX_TXN::where('campainFX_Txn.customerID', $request->customerID)
                                    ->where('campainFX_Txn.txnType', 'DEPOSIT')
                                    ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                    ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                    ->select('campainFX_Txn.id', 'campainFX.campainName', 'campainFX_Txn.txnType', 'campainFX_Txn.amount', 'campainFX_Txn.status') 
                                    ->get();

                //Customer Connection
                $customerConnection = CustomerConnection::where('cutomer_connection.customer_id', $request->customerID)
                                                    ->where('cutomer_connection.type', 'MT4')
                                                    ->where('cutomer_connection.status', 'ACTIVE')
                                                    ->get();

                return response()->json([
                                        'customer' => $customer, 
                                        'assetment' => $customerItem,
                                        'MLM'=> $tree, 
                                        'campaign'=> $CampainFXTXN_ID,
                                        'MT4' => $customerConnection,
                                        'message' => 'Get user successfully!'], 201);
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

    public function checkUserID(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'referalID' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Check if referalID exists
            $referalID = $request->referalID;
            $customer = Customer::find($referalID);
    
            if (!$customer) {
                return response()->json(['error' => 'Referral ID not found.'], 400);
            }
    
            // Build the referral data
            $referal = [
                'customer_id' => $customer->customer_id,
                'fullName' => $customer->full_name,
            ];
    
            return response()->json(['customer' => $referal, 'message' => 'Referral ID found successfully!'], 200);
            
        } catch (\Exception $e) {
            Log::error('Check user failed: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while checking user.'], 500);
        }
    }

    public function getAllCampaign(Request $request)
    {
        try {
            // Validate incoming request data
            $campainFXs = CampainFX::all();
             // Initialize an array to hold campaign data
            $data = [];

            // Loop through each campaign and add to the data array
            foreach ($campainFXs as $campainFX) {
                $data[] = [
                    'campainID' => $campainFX->campainID,
                    'campainName' => $campainFX->campainName,
                    'campainDescription' => $campainFX->campainDescription,
                    'campainAmount' =>  $campainFX->campain_amount,
                    'created_at' => $campainFX->created_at
                ];
            }
    
            return response()->json(['campaign' => $data, 'message' => 'Get All Campaign successfully!'], 200);
            
        } catch (\Exception $e) {
            Log::error('Get All Campaign Fail: ' . $e->getMessage());
            return response()->json(['error' => 'Get All Campaign Fail.'], 500);
        }
    }

}
