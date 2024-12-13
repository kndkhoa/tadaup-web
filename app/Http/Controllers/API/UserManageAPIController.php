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
use App\Models\Transaction_Temp;
use App\Models\CustomerAdditional;
use App\Models\CustomerReport;

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
                    'email' => $request->email ?? null,
                    'password' => Hash::make('12345678'),
                    'level' => 1,
                    'status' => 'ACT'
                ]);
                try {

                    //Create customer table
                    $path1 = '';
                    $path2 = '';
                    if ($request->hasFile('image_font') && $request->file('image_font')->isValid()) {
                        $path1 = $request->file('image_font')->store('dist/img/CCCD', 'public');
                    }
                    if ($request->hasFile('image_back') && $request->file('image_back')->isValid()) {
                        $path2 = $request->file('image_back')->store('dist/img/CCCD', 'public');
                    }
                    
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
                ],[
                    'customer_id' => $request->telegramid,
                    'type' => '6',
                    'value' =>  '0'
                ]
                ];
                foreach ($items as $item) {
                    CustomerItem::create($item);
                }
            });
            // $credentials = $request->only('email', 'password');
            // Auth::attempt($credentials);

            return response()->json(['user' => $request->telegramid, 'message' => 'Create user successfully!'], 201);
        } catch (\Exception $e) {
            Log::error('Create user failed: ' . $e->getMessage());
            return response()->json(['error' => 'Create user failed.'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'telegramid' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //Image
            $path1 = '';
            $path2 = '';
            if ($request->hasFile('image_font') && $request->file('image_font')->isValid()) {
                $path1 = $request->file('image_font')->store('dist/img/CCCD', 'public');
            }
            if ($request->hasFile('image_back') && $request->file('image_back')->isValid()) {
                $path2 = $request->file('image_back')->store('dist/img/CCCD', 'public');
            }

            //Update customer
            $customer = Customer::find($request->telegramid);
            if($customer){
                Customer::upsert(
                    [
                        'user_id' => $request->telegramid,
                        'customer_id' => $request->telegramid,
                        'role_id' => 2,
                        'full_name' => $request->name ?? $customer->full_name ?? null,
                        'phone' => $request->phone ?? $customer->phone ?? null,
                        'address' => $request->address ?? $customer->address ?? null,
                        'image_font_id' => $path1 ?? $customer->image_font_id ?? null,
                        'image_back_id' => $path2 ?? $customer->image_back_id ?? null,
                        'bank_account' => $request->bank_account ?? $customer->bank_account ?? null,
                        'bank_name' => $request->bank_name ?? $customer->bank_name ?? null,
                        'ewalletAddress' => $request->ewalletAddress ?? $customer->ewalletAddress ?? null,
                        'user_sponser_id' => $request->sponser_userid ?? $customer->user_sponser_id ?? null,
                    ],
                    ['user_id', 'customer_id'], // Keys to check for upsert
                    ['role_id', 'full_name', 'phone', 'address', 'image_font_id', 'image_back_id', 'bank_account', 'bank_name', 'ewalletAddress', 'user_sponser_id'] // Fields to update
                );
            }
            

            //Update user
            $user = USER::find($request->telegramid);
            if($user){
                USER::upsert(
                    [
                        'user_id' => $request->telegramid,
                        'username' => $request->name ?? $user->username ?? null,
                        'email' => $request->email ?? $user->email ?? null,
                        'password' => $request->password ? bcrypt($request->password) : $user->password,  // Handle password properly
                        'level' => $request->level ?? $user->level ?? null,
                        'status' => $request->status ?? $user->status ?? null,
                    ],
                    ['user_id'], // Keys to check for upsert
                    ['username', 'email', 'password', 'level', 'status'] // Fields to update
                );
            }

            // Update customer additional
            // Lấy bản ghi hiện tại từ cơ sở dữ liệu
            $currentCustomerAdditional = CustomerAdditional::where('customer_id', $request->telegramid)->first();
            // Prepare data for upsert using all fillable fields
            $data = [
                'customer_id' => $request->telegramid ?? $customerAdditional->telegramid,
                'gameScore' => $request->gameScore ?? $customerAdditional->gameScore ?? null,
                'gameChainCorrect' => $request->gameChainCorrect ?? $customerAdditional->gameChainCorrect ?? null,
                'gameTurn' => $request->gameTurn ?? $customerAdditional->gameTurn ?? null,
                'creditMainAddress' => $request->creditMainAddress ?? $customerAdditional->creditMainAddress ?? null,
                'creditSecondaryAddress' => $request->creditSecondaryAddress ?? $customerAdditional->creditSecondaryAddress ?? null,
                'creditDegree' => $request->creditDegree ?? $customerAdditional->creditDegree ?? null,
                'creditGender' => $request->creditGender ?? $customerAdditional->creditGender ?? null,
                'creditMarriage' => $request->creditMarriage ?? $customerAdditional->creditMarriage ?? null,
                'creditJob' => $request->creditJob ?? $customerAdditional->creditJob ?? null,
                'creditCompany' => $request->creditCompany ?? $customerAdditional->creditCompany ?? null,
                'creditCompanyDetail' => $request->creditCompanyDetail ?? $customerAdditional->creditCompanyDetail ?? null,
                'creditContactName1' => $request->creditContactName1 ?? $customerAdditional->creditContactName1 ?? null,
                'creditContactPhone1' => $request->creditContactPhone1 ?? $customerAdditional->creditContactPhone1 ?? null,
                'creditContactRelationship1' => $request->creditContactRelationship1 ?? $customerAdditional->creditContactRelationship1 ?? null,
                'creditContactName2' => $request->creditContactName2 ?? $customerAdditional->creditContactName2 ?? null,
                'creditContactPhone2' => $request->creditContactPhone2 ?? $customerAdditional->creditContactPhone2 ?? null,
                'creditContactRelationship2' => $request->creditContactRelationship2 ?? $customerAdditional->creditContactRelationship2 ?? null,
                'activeLastest' => $request->activeLastest ?? $customerAdditional->activeLastest ?? null,
                'activeOffline' => $request->activeOffline ?? $customerAdditional->activeOffline ?? null,
                'activeScore' => (int)($currentCustomerAdditional->activeScore ?? 0) + (int)($request->activeScore ?? 0), // Summing activeScore                'activeDiary' => $request->activeDiary, // Mảng mới từ request
                //'freetokenDone' => $request->freetokenDone, // Dữ liệu mới
                'proTrader' => $request->proTrader ?? $customerAdditional->proTrader ?? null,
            ];

            // // Xử lý `freetokenDone` để thêm phần tử mới vào mảng hiện tại
            // if ($currentCustomerAdditional) {
            //     $existingTokens = $currentCustomerAdditional->freetokenDone ?? []; // Lấy mảng hiện tại
            //     if (is_string($existingTokens)) {
            //         $existingTokens = json_decode($existingTokens, true) ?? []; // Chuyển đổi JSON thành mảng
            //     }

            //     if (is_string($data['freetokenDone'])) {
            //         $existingTokens[] = $data['freetokenDone']; // Thêm phần tử mới
            //     }

            //     $data['freetokenDone'] = json_encode($existingTokens); // Chuyển mảng thành JSON để lưu
            // } else {
            //     // Nếu chưa có bản ghi, khởi tạo mảng mới với phần tử từ request
            //     $data['freetokenDone'] = json_encode([$data['freetokenDone']]);
            // }

        //    // Xử lý activeDiary để thêm vào mảng hiện tại
        //     if ($currentCustomerAdditional) {
        //         // Lấy giá trị hiện tại từ cơ sở dữ liệu
        //         $existingActiveDiary = $currentCustomerAdditional->activeDiary ?? '[]'; // Nếu NULL thì khởi tạo mảng rỗng
        //         $existingActiveDiary = json_decode($existingActiveDiary, true); // Chuyển JSON thành mảng (nếu là JSON)

        //         if (is_array($data['activeDiary'])) {
        //             // Thêm mảng mới vào dữ liệu hiện tại
        //             $existingActiveDiary[] = $data['activeDiary'];
        //         }

        //         $data['activeDiary'] = json_encode($existingActiveDiary); // Chuyển mảng thành JSON để lưu
        //     } else {
        //         // Nếu không có dữ liệu, khởi tạo cột activeDiary với giá trị mới
        //         $data['activeDiary'] = json_encode([$data['activeDiary']]);
        //     }


            // Use upsert for inserting or updating
            CustomerAdditional::upsert(
                [$data],
                ['customer_id'], // Keys to check for upsert
                array_keys($data) // Fields to update
            );

            //Add customer connection
            if($request->link_url || $request->userNameMT4 || $request->passwordMT4){
                CustomerConnection::create([
                    'customer_id' => $request->telegramid,
                    'link_url' => $request->link_url ?? null,
                    'user_name' => $request->userNameMT4 ?? null,
                    'password' => $request->passwordMT4 ?? null,
                    'type' => $request->type ?? null,
                    'exchangeName' => $request->exchangeName ?? null,
                    'status' => 'ACTIVE'
                ]);
            }

            //Add  CustomerReport
            if($request->reportNet || $request->reportVolume || $request->reportExchange || $request->reportDate){
                CustomerReport::create([
                    'customer_id' => $request->telegramid,
                    'reportNet' => $request->reportNet ?? null,
                    'reportVolume' => $request->reportVolume ?? null,
                    'reportExchange' => $request->reportExchange ?? null,
                    'reportDate' => $request->reportDate ?? null
                ]);
            }

            return response()->json(['user' => $request->telegramid, 'message' => 'Update profile successfully!'], 201);
        } 
        catch (\Exception $e) {
            Log::error('Update profile failed: ' . $e->getMessage());
            return response()->json(['error' => 'Update profile failed.'], 500);
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
                                    ->select(
                                        'campainFX_Txn.id',
                                        'campainFX.campainName',
                                        'campainFX_Txn.txnType',
                                        'campainFX_Txn.amount',
                                        'campainFX_Txn.status'
                                    )
                                    ->get();

                //Customer additional
                $customerAdditional = CustomerAdditional::where('customer_id', $request->customerID)
                                            ->first();
                
                //Customer connection
                $customerConnection = CustomerConnection::where('customer_id', $request->customerID)
                                ->get();
                // Determine the role based on role_id
                $role = '';
                if ($customer->role_id == 1) {
                    $role = 'admin';
                } elseif ($customer->role_id == 2) {
                    $role = 'user';
                } elseif ($customer->role_id == 3) {
                    $role = 'proTrader';
                }
                return response()->json([
                                        'customer' => $customer, 
                                        'role' => $role,
                                        'additional' =>  $customerAdditional ?? null,
                                        'assetment' => $customerItem,
                                        'MLM'=> $tree, 
                                        'campaign'=> $CampainFXTXN_ID,
                                        'exchange' => $customerConnection,
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
            'id' => (string)$user->user_id,
            'text' => $user->full_name,
            'total_usdt' => (string)$this->getUSDTCustomer($user->user_id),
            'total_campaign' => (string)$this->getCountCampaign($user->user_id),
            'children' => []
        ];

        foreach ($user->children as $child) {
            $tree['children'][] = $this->buildTree($child);
        }

        return $tree;
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

    public function getCountCampaign($customerID)
    {
        try {
            $sumCampaign = CampainFX_Txn::where('customerID', $customerID)->count();

            return  $sumCampaign;
            
        } catch (\Exception $e) {
            Log::error('Count Campaign Customer ' . $customerID . 'Fail: ' . $e->getMessage());
            return response()->json(['error' => 'Get USDT Customer ' . $customerID . 'Fail: '], 500);
        }
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
            $campainFXs = CampainFX::join('customers', 'campainFX.origPerson', '=', 'customers.customer_id')
                                ->select('campainFX.*', 'customers.*');
            // Check if customerID is provided in the request
            if ($request->has('customerID') && $request->customerID) {
                $campainFXs->where('campainFX.origPerson', $request->customerID);
            }

            // Execute the query to get the results
            $campainFXs = $campainFXs->get();
             // Initialize an array to hold campaign data
            $data = [];

            // Loop through each campaign and add to the data array
            foreach ($campainFXs as $campainFX) {
                $data[] = [
                    'campainID' => $campainFX->campainID,
                    'campainName' => $campainFX->campainName,
                    'campainDescription' => $campainFX->campainDescription,
                    'campainAmount' =>  $campainFX->campain_amount,
                    'origPerson' => $campainFX->full_name,
                    'profitPercent' => $campainFX->profitPercent * 100 . '%',
                    'profitMLM' => $campainFX->profitMLM * 100 . '%',
                    'created_at' => $campainFX->created_at,
                    
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
                    $valueType4->value = $totalPoint == 0 ? $valueType3 :$totalPoint * $valueType3;
                    $valueType4->save();
                }
            }
            $this->calculateIncome($walletTadaup);
            Log::info('Calculate point MLM successfully!');
            return response()->json(['message' => 'Calculate point MLM successfully!'], 200);

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
                // Retrieve both type 4 and type 5 for the same customer
                $items = CustomerItem::where('customer_id', $customerItem->customer_id)
                    ->whereIn('type', [4, 5])
                    ->get();

                // Extract the current values for type 4 and type 5
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

    //interestAuto theo campaign WIN
    public function interestAuto()
    {
        try {
            $desired_status = ['WIN'];
            $type = 'DEPOSIT';
            $CampainFXTXNs = CampainFX_TXN::whereIn('campainFX_Txn.status', $desired_status)
                                ->where('campainFX_Txn.txnType', $type)
                                ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                 ->get();
            DB::transaction(function () use ($CampainFXTXNs) {              
                foreach( $CampainFXTXNs as  $CampainFXTXN){
                    $campainFX = CampainFX::where('campainID', $CampainFXTXN->campainID)
                                            ->firstOrFail(); 
                    $amount = ($campainFX->profitPercent * (double) $CampainFXTXN->amount)/30;
                    

                    $customerItemType1 = CustomerItem::where('customer_id', $CampainFXTXN->customerID)
                                        ->where('type', 1)
                                        ->firstOrFail()
                                        ->increment('value', (double)  $amount);
                        
                    $order_code = 'ORD' . Str::uuid()->toString();
                    Transaction_Temp::create([
                        'user_id' => $CampainFXTXN->customerID,
                        'type' => 'DEPOSIT',
                        'amount' => $amount,
                        'currency' => 'USDT',
                        'transactionHash' => $order_code,
                        'status' => 'DONE',
                        'eWallet' => '1',
                        'description' => 'Deposit interest auto from Campaign ' . $CampainFXTXN->campainID,
                        'origPerson' => 'Tada Auto'
                    ]);
                }
            });
            Log::info('Share interest auto successfully!');
            return response()->json(['message' => 'Share interest auto successfully!'], 200);
        } catch (\Exception $e) {
            Log::error('Share interest auto Fail: ' . $e->getMessage());
            return response()->json(['error' => 'Share interest auto Fail.'], 500);
        }
    }

    //interestAuto theo campaign WIN
    public function getListCampaignProTrader(Request $request)
    {
        try {
            $CampainFXTXN = CampainFX_TXN::where('campainFX.origPerson', $request->customerID)
                                        ->where('campainFX_Txn.txnType', 'DEPOSIT')
                                        ->where('campainFX_Txn.status', 'WIN')
                                        ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                        ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                        ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.customer_id')
                                                ->select(
                                                    'campainFX.campainID',
                                                    'campainFX.campainName',
                                                    'campainFX.profitPercent',
                                                    'campainFX.profitMLM',
                                                    'campainFX_Txn.customerID',
                                                    'customers.full_name as name',
                                                    'campainFX_Txn.amount',
                                                    'campainFX_Txn.status',
                                                    
                                                )
                                            ->get();
            $amountProfit = 0;
            $amountMLM = 0;
            $amountInvest=0;
            foreach($CampainFXTXN as $campainFXTXN_detail){
                $amountInterest = ($campainFXTXN_detail->profitPercent * (double) $campainFXTXN_detail->amount); //Lai theo ngay
                $amountProfit = $amountProfit + $amountInterest;

                $amountMLMDetail = ($campainFXTXN_detail->profitMLM * (double) $campainFXTXN_detail->amount); //Lai theo ngay
                $amountMLM = $amountMLM + $amountMLMDetail;

                $amountInvest = $amountInvest + (double) $campainFXTXN_detail->amount; // Tong tien dau tu
            }
            Log::info('Get List Campaign ProTrader successfully!');
            return response()->json(['data' => $CampainFXTXN,
                                    'totalAmountProfit' =>  (double)$amountProfit,
                                    'totalAmountMLM' =>  (double)$amountMLM,
                                    'totalAmountInvest' =>  (double)$amountInvest], 200);
        } catch (\Exception $e) {
            Log::error('Get List Campaign ProTrader Fail: ' . $request->customerID . $e->getMessage());
            return response()->json(['error' => 'Get List Campaign ProTrader Fail:'], 500);
        }
    }

}
