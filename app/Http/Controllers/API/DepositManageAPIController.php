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
                'description' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //Nap tien vi usdt 
            if ($request->ewallet == "1") {
                // Create a new order code for deposit
                $order_code = 'ORD' . Str::uuid()->toString();

                // Store transaction temporarily
                Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'DEPOSIT',
                    'amount' => $request->amount,
                    'currency' => 'USD',
                    'eWallet' => $request->ewallet,
                    'transactionHash' => $order_code,
                    'status' => 'WAIT',
                ]);

                return response()->json(['order' => $order_code, 'message' => 'Deposit wallet successfully!'], 201);
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
                ]);

                // Increment the value for the customer item with type = 1
                CustomerItem::where('customer_id', $request->customer_id)
                                ->where('type', 3)
                                ->increment('value', (double) $request->amount);

                return response()->json(['order' => $order_code, 'message' => 'Deposit wallet token successfully!'], 201);
            }

            //Dang ky thi quy
            if ($request->ewallet == "2") {
                // Transaction for withdrawal
                $order_code = 'ORD' . Str::uuid()->toString();
                $campainFX = CampainFX::where('campainID', $request->campainID)
                                        ->firstOrFail();  // Throws exception if not found                  
                $transaction_Temp = Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'WITHDRAW',
                    'amount' => $campainFX->campain_amount,
                    'currency' => 'USD',
                    'eWallet' => '1',
                    'transactionHash' => $order_code,
                    'status' => 'DONE',
                ]);
               // dd($campainFX->campain_amount);
                DB::transaction(function () use ($request, $campainFX) {
                    // Retrieve and update customer item for type = 1
                    $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 1)
                        ->firstOrFail();  // Throws exception if not found

                    if ((int)$customerItemType1->value < (int)$campainFX->campain_amount) {
                        throw new \Exception("Insufficient funds.");
                    }

                    // Decrement the value for type = 1
                    $customerItemType1->update([
                        'value' => (int) $customerItemType1->value - (int) $campainFX->campain_amount
                    ]);

                    // Retrieve and update customer item for type = 2
                    $customerItemType2 = CustomerItem::where('customer_id', $request->customer_id)
                        ->where('type', 2)
                        ->firstOrFail();  // Throws exception if not found

                    // Increment the value for type = 2
                    $customerItemType2->update([
                        'value' => (int) $customerItemType2->value + (int) $campainFX->campain_amount
                    ]);
                });

                // Create a new campaign transaction
                CampainFX_Txn::create([
                    'campainID' => $request->campainID,
                    'customerID' => $request->customer_id,
                    'ewalletCustomerID' => $request->ewallet,
                    'txnType' => 'DEPOSIT',
                    'amount' => $campainFX->campain_amount,
                    'txnDescription' => $request->description,
                    'transactionHash' => $order_code,
                    'status' => 'WAIT'
                ]);

                return response()->json(['message' => 'Register Fund successfully!'], 201);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle model not found exception (like firstOrFail)
            Log::error('Deposit wallet failed: ' . $e->getMessage());
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log any other exception
            Log::error('Deposit wallet failed: ' . $e->getMessage());
            return response()->json(['error' => 'Deposit wallet failed.'], 500);
        }
    }

    public function callbackDeposit(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            //'transactionAmount' => 'required|numeric',
            'customer_id' => 'required|integer',
            // 'transactionActiveDate' => 'required|string|max:500',
            // 'benefitAccountNumber' => 'required|string|max:500',
            // 'narrative' => 'required|string|max:500',
            // 'bankTransactionId' => 'required|string|max:500',
            // 'approvedBy' => 'required|string',
            // 'currency' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Check if the customer ID is '1'
            if ($request->customer_id === "1") {
                $walletTadaup = WalletTadaup::where('walletName', 'LIQUID')
                                            ->where('id', 2)
                                            ->first();

                if (!$walletTadaup) {
                    return response()->json(['error' => 'Wallet not found'], 404);
                }

                // Define the URL to check the hash
                $url = 'https://apilist.tronscanapi.com/api/transaction-info?hash=' . $request->bankTransactionId;
                $response = Http::get($url);
                // Check if the API call was successful
                if ($response->failed() || $response->body() === "{}\n") {
                    // Log the failed or empty response
                    Log::info('API returned empty response for bankTransactionId: ' . $request->bankTransactionId);

                    // Create a "WAIT" status transaction
                    Transaction_Temp::create([
                        'user_id' => $request->customer_id,
                        'type' => 'DEPOSIT',
                        'amount' => $request->transactionAmount,
                        'currency' => $request->currency,
                        'eWallet' => '1',
                        'status' => 'WAIT',
                        'bank_account' => $request->benefitAccountNumber ?? null,
                        'origPerson' => $request->approvedBy ?? null,
                        'transactionHash' => $request->bankTransactionId ?? null,
                        'description' => $request->narrative ?? null
                    ]);

                    return response()->json(['message' => 'Transaction is pending'], 202);
                }

                // Check response data validity
                if (
                    isset($response['contractData']['to_address']) &&
                    $response['contractData']['to_address'] === 'TQA2Z63x5rN561gCZSEnNPK5A5HK4W813s' &&
                    isset($response['contractData']['amount']) &&
                    (string)$response['contractData']['amount'] === (string)$request->transactionAmount
                ) {
                    // Perform database operations in a transaction
                    DB::transaction(function () use ($request) {
                        // Create the transaction record
                        Transaction_Temp::create([
                            'user_id' => $request->customer_id,
                            'type' => 'DEPOSIT',
                            'amount' => $request->transactionAmount,
                            'currency' => $request->currency,
                            'eWallet' => '1',
                            'status' => 'DONE',
                            'bank_account' => $request->benefitAccountNumber ?? null,
                            'origPerson' => $request->approvedBy ?? null,
                            'transactionHash' => $request->bankTransactionId ?? null,
                            'description' => $request->narrative ?? null
                        ]);

                        // Update the wallet value
                        WalletTadaup::where('walletName', 'LIQUID')
                            ->where('id', 2)
                            ->increment('value', $request->transactionAmount);
                    });

                    return response()->json(['message' => 'Transaction completed successfully!'], 200);
                } else {
                    return response()->json(['error' => 'Transaction validation failed'], 400);
                }
            }

            // If the customer ID is not '1', create a transaction for a regular customer
            DB::transaction(function () use ($request) {
                Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'DEPOSIT',
                    'amount' => $request->transactionAmount,
                    'currency' => $request->currency,
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



    
}
