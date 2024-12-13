<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction_Temp;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\CustomerItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\WalletTadaup;

class WithdrawManageAPIController extends Controller
{
    
    public function withdrawOrder(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|string|min:1',
                'amount' => 'required|string|max:255',
                'eWallet' => 'required|string|max:500',
               // 'description' => 'required|string|max:500',
                'currency' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            if($request->currency === 'USDT'){
                //$customerByID = Customer::find($request->customer_id);
                // Retrieve and update customer item for type = 1
                $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                                        ->where('type', 1)
                                        ->firstOrFail();  // Throws exception if not found
    
                if ((double)$customerItemType1->value < (double)$request->amount) {
                    return response()->json(['error' => 'Insufficient funds.'], 500);
                }

    
                // Create a new order
                $transaction_temp = transaction_temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'WITHDRAW',
                    'amount' => $request->amount,
                    'currency' => $request->currency ?? null,
                    'eWallet' => $request->eWallet ?? null, 
                    'description' => $request->description ?? null,
                    'status' => 'WAIT'
                ]);
                return response()->json(['message' => 'Withdraw created successfully!'], 201);
            }
            else if($request->currency === 'VND'){
                
                 // Retrieve and update customer item for type = 1
                 $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                    ->where('type', 1)
                    ->firstOrFail();  // Throws exception if not found

                if ((double)$customerItemType1->value < (double)$request->amount) {
                    return response()->json(['error' => 'Insufficient funds.'], 500);
                }

                DB::transaction(function () use ($request) {
                    $order_code = 'ORD' . Str::uuid()->toString();
                    //Amount VND
                    $amount = (double)$request->amount * 26000;

                    // Create a new order
                    $transaction_temp = transaction_temp::create([
                        'user_id' => $request->customer_id,
                        'type' => 'WITHDRAW',
                        'amount' => $amount,
                        'currency' => $request->currency ?? null,
                        'eWallet' => $request->eWallet ?? null, 
                        'description' => $request->description ?? null,
                        'transactionHash' => $order_code,
                        'status' => 'WAIT',
                        'bank_account' => $request->bank_account ?? null,
                        'bank_name'=> $request->bank_name ?? null,
                        'bank_city'=> $request->bank_code ?? null,
                        'fullname' => $request->fullname ?? null,
                    ]);
                    $response = $this->withdrawAmountByGateway(env('EMAIL_GW'), env('PASSWORD_GW'), $request, $order_code, $amount);
                    Log::info('Response GW OPENE: ' .$response['success'] . ' ' . $response['message']);
                    //dd($response['success']);
                    
                });
                return response()->json(['message' => 'Withdraw created successfully!'], 201);
            }
            

        } catch (\Exception $e) {
            Log::error('Create Order Withdraw failed: ' .$request->customer_id . ' with error ' . $e->getMessage());
            return response()->json(['error' => 'Exeption Create Order Withdraw failed.'], 500);
        }
    }


    //Call api Gateway 3rd
    public function withdrawAmountByGateway($email, $password, $transferData, $order_code, $amount)
    {
        // Step 1: Login to get Access Token
        $loginResponse = Http::post('https://api-payment.opene.io/v1/auth/login', [
            'email' => $email,
            'password' => $password
        ]);
        if ($loginResponse->failed()) {
            Log::error('Login API failed: ' . $loginResponse->body());
            return response()->json(['error' => 'Login failed'], 401);
        }
        
        $loginData = $loginResponse->json();
        
        // Check if 'tokens' and 'access' fields exist
        if (!isset($loginData['tokens']['access']['token'])) {
            Log::error('Access token not found in response: ' . json_encode($loginData));
            return response()->json(['error' => 'Access token not found'], 401);
        }

        $accessToken = $loginData['tokens']['access']['token'];

        // Step 2: Perform the transfer using the Access Token
        $transactionResponse = Http::withToken($accessToken)
            ->post('https://api-payment.opene.io/v1/merchant/transaction/transfer-create-v2', [
                'account_no' => $transferData['bank_account'],
                'amount' => $amount,
                'bank_name' => $transferData['bank_name'],
                'description' => $transferData['description'],
                'customer_id' => $transferData['customer_id'],
                'transaction_hash' => $order_code,
            ]);
            
        if ($transactionResponse->failed()) {
            Log::error('Transaction API failed: ' . $transactionResponse->body());
            return response()->json(['error' => 'Transaction failed'], 400);
        }

        // Return success response
        return $transactionData = $transactionResponse->json();
    }

    public function callbackWithdraw(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|string',
                'transaction_hash' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $transaction_temp = transaction_temp::where('transactionHash', $request->transaction_hash)
                                                ->where('user_id', $request->customer_id)
                                        ->first();  // Use first() to get a single record
 
            if (!$transaction_temp) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Update the record if it exists
            $transaction_temp->update(['status' => 'DONE', 'origPerson' => $request->bankTransactionId]);
            return response()->json(['message' => 'Callback successfully!'], 201);

        } catch (\Exception $e) {
            Log::error('Callback failed: ' . $e->getMessage());
            return response()->json(['error' => 'Callback failed.'], 500);
        }
    }


    public function approve(Request $request)
    {
        try {
            $transaction_temp = Transaction_Temp::where([
                                                        ['user_id', '=', $request->customer_id],
                                                        ['status', '=', 'WAIT'],
                                                        ['amount', '=', $request->amount],
                                                        ['type', '=', 'WITHDRAW']
                                                    ])->first();
            

            
            DB::transaction(function () use ($transaction_temp) {
                $desired_status = 'DONE';
                // Update the transaction status and set the originating person
                $transaction_temp->update([
                    'status' => $desired_status,
                    'origPerson' => 'ANAN',
                ]);

                //Vi USDT khach
                CustomerItem::where('customer_id', $transaction_temp->user_id)
                                    ->where('type', 1)
                                    ->decrement('value', (double) $transaction_temp->amount);


                //Vi INCOME Tada
                WalletTadaup::where('walletName', 'LIQUID')
                                    ->where('id', 2)
                                    ->decrement('value', (double) $transaction_temp->amount);

            });
            Log::info('Customer ID' . $transaction_temp->user_id . ' approved and updated transaction withdraw successfully.');
            return response()->json(['message' => 'Customer ID' . $transaction_temp->user_id . ' approved and updated transaction withdraw successfully.'], 201);
        } catch (\Exception $e) {
            Log::error('Customer ID' . $transaction_temp->user_id . ' approved and updated transaction withdraw fail.');
            return response()->json(['error' => 'Customer ID' . $transaction_temp->user_id . ' approved and updated transaction withdraw fail.'], 500);
        }
    }

    
}
