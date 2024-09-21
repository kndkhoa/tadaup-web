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
use App\Models\WalletTadaup;
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
                //'email' => 'required|email|max:250|unique:users',
                //'password' => 'required|min:8',
                //'fullname' => 'required|string|max:250',
                //'phone' => 'required|string|max:250',
                //'ewalletAddress' => 'required|string|max:250',
                //'image_font' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                //'image_back' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                //'bank_account' => 'required|string|max:250',
                //'bank_name' => 'required|string|max:250'
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
                    'username' => $request->name ?? '',
                    'email' => $request->email ?? '',
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
                        'full_name' => $request->fullname ?? '',
                        'phone' => $request->phone ?? '',
                        'image_font_id' => $path1 ?? '',
                        'image_back_id' => $path2 ?? '',
                        'ewalletAddress' => $request->ewalletAddress ?? '',
                        'ewalletNetwork' => 'TON',
                        'bank_account' => $request->bank_account ?? '',
                        'bank_name' => $request->bank_name ?? ''
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
                ],
                [
                    'customer_id' => $request->telegramid,
                    'type' => '5',
                    'value' =>  '0'
                ]
                ];
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
                                    ->leftJoin('cutomer_connection', 'campainFX_Txn.transactionHash', '=', 'cutomer_connection.transactionHash') // Join on transactionHash
                                    ->select(
                                        'campainFX_Txn.id',
                                        'campainFX.campainName',
                                        'campainFX_Txn.txnType',
                                        'campainFX_Txn.amount',
                                        'campainFX_Txn.status',
                                        'cutomer_connection.report as report',  // Include customer_connection data
                                        //'cutomer_connection.user_name as user_name',  // Check if a connection exists
                                        //'cutomer_connection.password as password',
                                        //'cutomer_connection.created_at as created_at'
                                    )
                                    ->get();

                
                return response()->json([
                                        'customer' => $customer, 
                                        'assetment' => $customerItem,
                                        'MLM'=> $tree, 
                                        'campaign'=> $CampainFXTXN_ID,
                                        //'MT4' => $customerConnection,
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
            'total_usdt' => $this->getUSDTCustomer($user->user_id),
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

    public function calculatePoint()
    {
        try {
            $customerItems = CustomerItem::where('type', 4)->get();
            $walletTadaup = WalletTadaup::where('walletName', 'INCOME')->first();
            foreach ($customerItems as $customerItem) {
                // Calculate the new total point for the current customer
                $totalPoint = $this->calculateUSDTMLM($customerItem->customer_id);

                // Retrieve both type 3 and type 4 for the same customer
                $items = CustomerItem::where('customer_id', $customerItem->customer_id)
                    ->whereIn('type', [3, 4])
                    ->get();

                // Extract the current values for type 3 and type 4
                $valueType3 = $items->firstWhere('type', 3)->value ?? 0;  // Default to 0 if not found
                $valueType4 = $items->firstWhere('type', 4);

                // Update type 4 by adding $totalPoint to the value of type 3
                if ($valueType4) {
                    $valueType4->value = $totalPoint = 0 ? $valueType3 :$totalPoint * $valueType3;
                    $valueType4->save();
                }
            }
            $this->calculateIncome($walletTadaup);

            return response()->json(['message' => 'Calculate point successfully!'], 200);

        } catch (\Exception $e) {
            Log::error('Calculate point Fail: ' . $e->getMessage());
            return response()->json(['error' => 'Calculate point Fail.'], 500);
        }
    }
    
    public function calculateIncome($walletTadaup){
        try {
            // Sum Total Point for type = 4
            $totalValue = CustomerItem::where('type', 4)->sum('value');

            // Ensure the total value is not zero to avoid division by zero
            if ($totalValue > 0) {
                // Calculate the mining ratio as a double
                $mining = (double) $walletTadaup->value / (double) $totalValue;
            } else {
                // Handle case where totalValue is zero to avoid division by zero
                $mining = 0;
            }

            $customerItems = CustomerItem::where('type', 4)->get();
            foreach ($customerItems as $customerItem) {
                // Retrieve both type 3 and type 4 for the same customer
                $items = CustomerItem::where('customer_id', $customerItem->customer_id)
                    ->whereIn('type', [4, 5])
                    ->get();

                // Extract the current values for type 3 and type 4
                $valueType4 = $items->firstWhere('type', 4)->value ?? 0;  // Default to 0 if not found
                $valueType5 = $items->firstWhere('type', 5);

                // Update type 4 by adding $totalPoint to the value of type 3
                if ($valueType5) {
                    $valueType5->value = $mining * $valueType4;
                    $valueType5->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('calculateIncome Fail: ' . $e->getMessage());
            return response()->json(['error' => 'calculateIncome Fail.'], 500);
        }
    }

    public function calculateUSDTMLM($customerID)
    {
        try {
            //tree
            $tree = null;
            $userTree = Customer::with('children')->find($customerID);
            if (!$userTree) {
                $tree = [];
            }
            else{
                $tree = $this->buildTree($userTree);
            }
            $totalWeightedUSDT = $this->calculateTotalUSDT($tree);
            return $totalWeightedUSDT;
            
        } catch (\Exception $e) {
            Log::error('calculateUSDTMLM Fail: ' . $e->getMessage());
            return response()->json(['error' => 'calculateUSDTMLM Fail.'], 500);
        }
    }

    public function getUSDTCustomer($customerID)
    {
        try {
            //Customer item
            $results = DB::table('customer_item')
                            ->select(
                                DB::raw('SUM(CASE WHEN type IN (1, 2) THEN value ELSE 0 END) AS sum_type_1_2'),
                                DB::raw('(SELECT value FROM customer_item WHERE customer_id = ' . $customerID . ' AND type = 3 LIMIT 1) AS value_type_3'),
                                DB::raw('(SELECT value FROM customer_item WHERE customer_id = ' . $customerID . ' AND type = 4 LIMIT 1) AS value_type_4')
                            )
                            ->where('customer_id', $customerID)
                            ->first();
            $customer_usdt = $results->sum_type_1_2 ?? 0;
            $customer_token = $results->value_type_3;
            $customer_point = $results->value_type_4;       
            return  $customer_usdt;
            
        } catch (\Exception $e) {
            Log::error('Get USDT Customer ' . $customerID . 'Fail: ' . $e->getMessage());
            return response()->json(['error' => 'Get USDT Customer ' . $customerID . 'Fail: '], 500);
        }
    }

    function calculateTotalUSDT($array, $level = 1) {
        // Define multipliers based on the level
        $multipliers = [
            1 => 5,
            2 => 4,
            3 => 3,
            4 => 2,
            5 => 1
        ];
    
        // Multiply the current node's `total_usdt` by 10
        $total = $array['total_usdt'] * 10;
    
        // If the current node has children, calculate their total_usdt
        if (!empty($array['children'])) {
            foreach ($array['children'] as $child) {
                // Use the appropriate multiplier for the current level of children
                $multiplier = $multipliers[$level] ?? 1; // Default to 1 if level exceeds 5
                $total += $this->calculateChildrenUSDT($child, $level + 1, $multiplier);
            }
        }
    
        return $total;
    }

    function calculateChildrenUSDT($child, $level, $multiplier) {
        // Multiply the child's `total_usdt` by the given multiplier
        $total = $child['total_usdt'] * $multiplier;
    
        // If the child has its own children, calculate recursively
        if (!empty($child['children'])) {
            foreach ($child['children'] as $subChild) {
                // Use the correct multiplier for subchildren, reducing it by 1
                $newMultiplier = max($multiplier - 1, 1);  // Ensure the multiplier doesn't go below 1
                $total += $this->calculateChildrenUSDT($subChild, $level + 1, $newMultiplier);
            }
        }
    
        return $total;
    }

}
