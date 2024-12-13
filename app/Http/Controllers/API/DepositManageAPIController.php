<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampainFX_Txn;
use App\Models\CampainFX;
use App\Models\Customer;
use App\Models\CustomerItem;
use App\Models\Transaction_Temp;
use App\Models\WalletTadaup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\CustomerAdditional;



class DepositManageAPIController extends Controller
{
    
    public function deposit(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                //'amount' => 'required|numeric|min:0.01',
                'customer_id' => 'required|string|min:1',
                'ewallet' => 'required|string|max:500',
                //'description' => 'required|string|max:500',
                //'transactionHash' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //Nap tien vi usdt 
            if ($request->ewallet == "1") {
                if($request->customer_id === '6161091565'){
                    CustomerItem::where('customer_id', $request->customer_id)
                                ->where('type', 1)
                                ->increment('value', (double) $request->amount);
                    return response()->json(['transactionHash' => $request->transactionHash, 'message' => 'Deposit wallet successfully!'], 201);
                }
                else{
                    $wallet_tada =  WalletTadaup::where('walletName', 'LIQUID')
                    ->where('id', 2)->first();

                    // Create a new order code for deposit
                    $transaction_Temp_ID = Transaction_Temp::where('transactionHash', $request->transactionHash)
                                                            ->first();
                    if($transaction_Temp_ID){
                        return response()->json(['Error' => 'transactionHash: ' .$request->transactionHash .' exists'], 400);
                    }

                    // Store transaction temporarily
                    $transaction_Temp = Transaction_Temp::create([
                        'user_id' => $request->customer_id,
                        'type' => 'DEPOSIT',
                        'amount' => $request->amount,
                        'currency' => 'USDT',
                        'eWallet' => $request->ewallet,
                        'transactionHash' => $request->transactionHash,
                        'status' => 'WAIT',
                        'origPerson' => $request->customer_id,
                    ]);

                    $check = $this->checkTransactionByHash($request->transactionHash);
                    if($check){
                        $amountStr = $check[0]['amount_str'];
                        $decimals = $check[0]['decimals'];

                        // Convert amount_str using the token's decimals
                        $amountInDecimals = floatval($amountStr) / pow(10, $decimals);

                        // Format the amount by removing trailing zeros and decimal point if it's a whole number
                        $formattedAmount = (intval($amountInDecimals) == $amountInDecimals) 
                            ? intval($amountInDecimals) 
                            : number_format($amountInDecimals, $decimals);
                        
                        if($check[0]['to_address'] === $wallet_tada->address ){//$wallet_tada->address){ //'TRdPZ3SqzakBGk2HECrUrWe5mtsDHLdFmG'){
                            $transaction_Temp->update(['status' => 'DONE'
                                                ]);
                            
                            
                            $wallet_tada->increment('value', (double) $formattedAmount);
                            if($transaction_Temp->user_id !== "1"){
                                CustomerItem::where('customer_id', $transaction_Temp->user_id)
                                ->where('type', 1)
                                ->increment('value', (double) $formattedAmount);
                            }

                            Transaction_Temp::create([
                                'user_id' => '1',
                                'type' => 'DEPOSIT',
                                'amount' => $request->amount,
                                'currency' => 'USDT',
                                'eWallet' => '2',
                                'transactionHash' => $request->transactionHash,
                                'status' => 'DONE',
                                'description' => 'Nap tu '. $request->customer_id
                            ]);

                            return response()->json(['transactionHash' => $request->transactionHash, 'message' => 'Deposit wallet successfully!'], 201);
                        }
                    }
                }
                
            }

            //Token
            if ($request->ewallet == "3") {
                // Create a new order code for deposit
                $order_code = 'ORD' . Str::uuid()->toString();

                // Store transaction temporarily
                Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'DEPOSIT',
                    'amount' => $request->amount,
                    'currency' => 'TDU',
                    'eWallet' => '3',
                    'transactionHash' => $order_code,
                    'status' => 'DONE',
                    'origPerson' => 'Training',
                ]);

                // Increment the value for the customer item with type = 1
                CustomerItem::where('customer_id', $request->customer_id)
                                ->where('type', 3)
                                ->increment('value', (double) $request->amount);

                return response()->json(['order' => $order_code, 'message' => 'Deposit wallet token successfully!'], 201);
            }

            //Dang ky thi quy
            if ($request->ewallet == "2") {
                $order_code = 'ORD' . Str::uuid()->toString();
                // Transaction for withdrawal
                $campainFX = CampainFX::where('campainID', $request->campainID)
                                        ->firstOrFail();  // Throws exception if not found  
                if (!$campainFX) {
                    return response()->json(['error' => $request->campainID . ' not exists.'], 500);
                }                
               
                
                DB::transaction(function () use ($request, $campainFX, $order_code) {

                    //Step 1. Save transaction History ví khách
                    $transaction_Temp = Transaction_Temp::create([
                        'user_id' => $request->customer_id,
                        'type' => 'WITHDRAW',
                        'amount' => $request->amount,
                        'currency' => 'USDT',
                        'eWallet' => '1',
                        'transactionHash' => $order_code,
                        'status' => 'DONE',
                    ]);

                    $transaction_Temp = Transaction_Temp::create([
                        'user_id' => $request->customer_id,
                        'type' => 'DEPOSIT',
                        'amount' => $request->amount,
                        'currency' => 'USDT',
                        'eWallet' => '2',
                        'transactionHash' => $order_code,
                        'status' => 'DONE',
                    ]);

                    // Retrieve and update customer item for type = 1
                    $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 1)
                        ->firstOrFail();  // Throws exception if not found

                    if ((double)$customerItemType1->value < (double)$request->amount) {
                        Log::error('Insufficient funds: CustomerItem Value = ' . $customerItemType1->value . ', Requested Amount = ' . $request->amount);
                        throw new \Exception('Insufficient funds.'); 
                    }

                    // Decrement the value for type = 1
                    $customerItemType1->update([
                        'value' => (double) $customerItemType1->value - (double) $request->amount
                    ]);

                    // Retrieve and update customer item for type = 2
                    $customerItemType2 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 2)
                        ->firstOrFail();  // Throws exception if not found

                    // Increment the value for type = 2
                    $customerItemType2->update([
                        'value' => (double) $customerItemType2->value + (double) $request->amount
                    ]);

                    //Step 2. Save transaction History ví pro trader
                    $currentDate = Carbon::now(); // Get the current date
                    $endOfMonth = $currentDate->endOfMonth(); // Get the last day of the month
                    $remainingDays = Carbon::now()->diffInDays(Carbon::now()->endOfMonth());

                    $amountAvg = ($campainFX->profitPercent * (double) $request->amount)/30;
                    $amount = (double)$amountAvg * $remainingDays;
                    $amount = (double)$request->amount - (double)$amount;
                    $transaction_Temp = Transaction_Temp::create([
                        'user_id' => $campainFX->origPerson,
                        'type' => 'DEPOSIT',
                        'amount' => $amount, //amoun tru lãi
                        'currency' => 'USDT',
                        'eWallet' => '1',
                        'transactionHash' => $order_code,
                        'status' => 'DONE',
                    ]);

                    // Retrieve and update customer item for type = 1
                    $customerItemType1 = CustomerItem::where('customer_id', $campainFX->origPerson)
                        ->where('type', 1)
                        ->firstOrFail();  // Throws exception if not found
                    // Increment the value for type = 1
                    $customerItemType1->update([
                        'value' => (double) $customerItemType1->value + (double) $amount
                    ]);


                });

                // Create a new campaign transaction
                CampainFX_Txn::create([
                    'campainID' => $request->campainID,
                    'customerID' => $request->customer_id,
                    'ewalletCustomerID' => $request->ewallet,
                    'txnType' => 'DEPOSIT',
                    'amount' => $request->amount,
                    'txnDescription' => $request->description,
                    'transactionHash' => $order_code,
                    'status' => 'DONE',
                    'origPerson' => 'ANAN',
                ]);

                Log::info(['message' => 'Customer ' . $request->customer_id . ' register Fund successfully with CampaignID ' . $request->campainID,
                            '$order_code' => $order_code]);
                return response()->json(['message' => 'Customer ' . $request->customer_id . ' register Fund successfully with CampaignID ' . $request->campainID,  '$order_code' => $order_code], 201);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle model not found exception (like firstOrFail)
            Log::error('Deposit wallet failed: ' . $e->getMessage());
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log any other exception
            Log::error('Deposit wallet failed: ' . $e->getMessage());
            return response()->json(['error' =>  $request->customer_id . 'Deposit wallet failed.'], 500);
        }
    }

    public function swap(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|string|min:1',
                'fromToken' => 'required|string|max:500',
                'toToken' => 'required|string|max:500',
                'amount' => 'required|string',
                
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $order_code = 'ORD' . Str::uuid()->toString();

            //Kiem tra so du fromToken
            if($request->fromToken === 'TDU' && $request->toToken === 'USDT'){
                //Get so du vi TDU
                $customerItemType3 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 3)
                        ->firstOrFail();
                //Get so du vi activeCore
                $activeCore =  CustomerAdditional::where('customer_id', $request->customer_id)
                                ->firstOrFail();

                if((double)$customerItemType3->value <= 0 || (double)$customerItemType3->value < $request->amount){
                    Log::error('fromToken ' . $request->fromToken . ' not enough!' );
                    return response()->json(['error' => 'fromToken ' . $request->fromToken . ' not enough!'], 404);
                }
                if((double)$activeCore->activeScore < $request->amount){
                    Log::error('ActiveCore less than ' . $request->amount );
                    return response()->json(['error' => 'ActiveCore less than ' . $request->amount], 404);
                }

                DB::transaction(function () use ($request, $order_code) {
                        // Store transaction temporarily WITH
                        Transaction_Temp::create([
                            'user_id' => $request->customer_id,
                            'type' => 'WITHDRAW',
                            'amount' => $request->amount,
                            'currency' => $request->fromToken,
                            'eWallet' => '3',
                            'transactionHash' => $order_code,
                            'status' => 'DONE',
                            'origPerson' => 'ANAN',
                        ]);
                        // Decrement the value for the customer item with type = 3
                        CustomerItem::where('customer_id', $request->customer_id)
                                    ->where('type', 3)
                                    ->decrement('value', (double) $request->amount);
    
                        Transaction_Temp::create([
                            'user_id' => $request->customer_id,
                            'type' => 'DEPOSIT',
                            'amount' => $request->amount,
                            'currency' => $request->toToken,
                            'eWallet' => '1',
                            'transactionHash' => $order_code,
                            'status' => 'DONE',
                            'origPerson' => 'ANAN',
                        ]);
                        // Increment the value for the customer item with type = 1
                        CustomerItem::where('customer_id', $request->customer_id)
                                    ->where('type', 1)
                                    ->increment('value', (double) $request->amount);

                        // Decrement the value active Core
                        CustomerAdditional::where('customer_id', $request->customer_id)
                                    ->decrement('activeScore', (double) $request->amount);
                });
            }
            if($request->fromToken === 'USDT' && $request->toToken === 'TDU'){
                //Get so du vi USDT
                $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 1)
                        ->firstOrFail();

                if((double)$customerItemType1->value <= 0 || (double)$customerItemType1->value < $request->amount){
                    Log::error('fromToken ' . $request->fromToken . ' not enough!' );
                    return response()->json(['error' => 'fromToken ' . $request->fromToken . ' not enough!'], 404);
                }

                DB::transaction(function () use ($request, $order_code) {
                        // Store transaction temporarily WITH
                        Transaction_Temp::create([
                            'user_id' => $request->customer_id,
                            'type' => 'WITHDRAW',
                            'amount' => $request->amount,
                            'currency' => $request->fromToken,
                            'eWallet' => '1',
                            'transactionHash' => $order_code,
                            'status' => 'DONE',
                            'origPerson' => 'ANAN',
                        ]);
                        // Decrement the value for the customer item with type = 3
                        CustomerItem::where('customer_id', $request->customer_id)
                                    ->where('type', 1)
                                    ->decrement('value', (double) $request->amount);
    
                        Transaction_Temp::create([
                            'user_id' => $request->customer_id,
                            'type' => 'DEPOSIT',
                            'amount' => $request->amount,
                            'currency' => $request->toToken,
                            'eWallet' => '3',
                            'transactionHash' => $order_code,
                            'status' => 'DONE',
                            'origPerson' => 'ANAN',
                        ]);
                        // Increment the value for the customer item with type = 1
                        CustomerItem::where('customer_id', $request->customer_id)
                                    ->where('type', 3)
                                    ->increment('value', (double) $request->amount);
                });
            }
            
            Log::info(['message' => 'Customer ' . $request->customer_id . ' Swap Token successfully',
                                        '$order_code' => $order_code]);
            return response()->json(['message' => 'Customer ' . $request->customer_id . ' Swap Token successfully', '$order_code' => $order_code], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle model not found exception (like firstOrFail)
            Log::error('Swap Token failed: ' . $e->getMessage());
            return response()->json(['error' => 'Swap Token Fail.'], 404);
        } catch (\Exception $e) {
            // Log any other exception
            Log::error('Deposit wallet failed: ' . $e->getMessage());
            return response()->json(['error' =>  $request->customer_id . ' Swap Token failed.'], 500);
        }
    }

    public function callbackDeposit(Request $request){
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            //'transactionAmount' => 'required|numeric',
            'customer_id' => 'required|integer',
            // 'transactionActiveDate' => 'required|string|max:500',
            // 'benefitAccountNumber' => 'required|string|max:500',
            // 'narrative' => 'required|string|max:500',
            // 'bankTransactionId' => 'required|string|max:500',
            // 'approvedBy' => 'required|string',
            // 'currency' => 'required|string',
            // 'transactionHash' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // If the customer ID is not '1', create a transaction for a regular customer
            DB::transaction(function () use ($request) {
                Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'DEPOSIT',
                    'amount' => $request->transactionAmount,
                    'currency' => $request->currency ?? 'USDT',
                    'eWallet' => '1',
                    'status' => 'DONE',
                    'bank_account' => $request->benefitAccountNumber ?? null,
                    'origPerson' => $request->approvedBy ?? null,
                    'transactionHash' => $request->bankTransactionId ?? null,
                    'description' => $request->narrative ?? null
                ]);

                // Update the customer item value
                CustomerItem::where('customer_id', $request->customer_id)
                            ->where('type', 1)
                            ->increment('value', $request->transactionAmount);
            });

            return response()->json(['message' => 'Callback deposit successfully!'], 201);
        } catch (\Exception $e) {
            // Log the exception and return an error response
            Log::error('Callback deposit failed: ' . $e->getMessage());
            return response()->json(['error' => 'Callback failed: ' . $e->getMessage()], 500);
        }
    }

    public function callbackDepositLiquid(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string',
            'transactionHash' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaction_Temp_ID = Transaction_Temp::where('transactionHash', $request->transactionHash)
                                            ->first();
        if($transaction_Temp_ID){
            return response()->json(['Error' => 'transactionHash: ' .$request->transactionHash .' exists'], 400);
        }
    
        try {
            // Check if the customer ID is '1'
            if ($request->customer_id == 1) {
                return $this->createPendingTransaction($request, 'Transaction is pending', 202);
            }
    
        } catch (\Exception $e) {
            // Log the exception and return an error response
            Log::error('Callback deposit failed: ' . $e->getMessage());
            return response()->json(['error' => 'Callback failed: ' . $e->getMessage()], 500);
        }
    }
    
    // Helper method to create a pending transaction
    private function createPendingTransaction($request, $message, $statusCode)
    {
        Transaction_Temp::create([
            'user_id' => $request->customer_id,
            'type' => 'DEPOSIT',
            'amount' => $request->transactionAmount,
            'currency' => $request->currency ?? 'USDT',
            'eWallet' => '1',
            'status' => 'WAIT',
            'bank_account' => $request->benefitAccountNumber ?? null,
            'origPerson' => $request->approvedBy ?? null,
            'transactionHash' => $request->transactionHash,
            'description' => $request->narrative ?? null
        ]);
    
        return response()->json(['message' => $message], $statusCode);
    }
    

    //Check Ton Network
    function checkTransactionByHash($transactionHash)
    {
        try {
            // Define the URL to check the hash
            $url = 'https://apilist.tronscanapi.com/api/transaction-info?hash=' . $transactionHash;
            $response = Http::get($url);
            if ($response->failed() || empty($response->body())) {
                Log::info('API returned empty response for transactionHash: ' . $transactionHash);
                return null;
            }
            
            // Check if TRC20 transfer info is present (optional based on transaction type)
            if (!isset($response['trc20TransferInfo'])) {
                Log::info('Transaction does not involve TRC20 token transfer: ' . $transactionHash);
                // Handle TRX transfers or other non-TRC20 transactions here
                return null;
            }
            return $response['trc20TransferInfo'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
}
