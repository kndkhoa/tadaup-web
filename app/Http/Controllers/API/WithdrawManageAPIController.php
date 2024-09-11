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


class WithdrawManageAPIController extends Controller
{
    
    public function withdrawOrder(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|integer|min:1',
                'amount' => 'required|string|max:255',
                'eWallet' => 'required|string|max:500',
                'payee_bank_code' => 'required|string|max:500',
                'payee_bank_account_no' => 'required|string|max:500',
                'payee_bank_account_name' => 'required|string|max:500',
                'description' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order_code = 'ORD' . Str::uuid()->toString();
            $customerByID = Customer::find($request->input('customer_id'));
            // Create a new order
            $transaction_temp = transaction_temp::create([
                'user_id' => $request->input('customer_id'),
                'type' => 'WITHDRAW',
                'amount' => $request->input('amount'),
                'currency' => 'USD',
                'eWallet' => $request->input('eWallet'),
                'transactionHash' => $order_code,
                'bank_name' => $request->input('payee_bank_code'),
                'bank_account' => $request->input('payee_bank_account_no'),
                'fullname' => $request->input('payee_bank_account_name'),
                'status' => 'ORIG'

            ]);

            $createOrder = self::callAPIPartnerWithdraw($order_code, $request);
            if($createOrder['code'] = 200){
                $transaction_temp->update(['status' => 'WAIT'
                ]);
                // Return a success response with the created order
                return response()->json(['order' => $createOrder, 'message' => 'Withdraw created successfully!'], 201);
            }

            return response()->json(['error' => 'Create Order Withdraw failed.'], 500);

        } catch (\Exception $e) {
            Log::error('Create Order Deposit failed: ' . $e->getMessage());
            return response()->json(['error' => 'Exeption Create Order Withdraw failed.'], 500);
        }
    }

    public function callbackWithdraw(Request $request)
    {
        try {
            $transaction_temp = transaction_temp::where('transactionHash', $request->input('partner_order_code'))
                                        ->first();  // Use first() to get a single record
    
            $status = $request->input('transfer_record.status');
        
            if ($status == "2" && $transaction_temp) {
                // Update the record if it exists
                $transaction_temp->update(['status' => 'DONE', 'errorMsg' => '']);
                return response()->json(['message' => 'Callback successfully!'], 201);
            }
            else if ($status == "3" && $transaction_temp) {
                $errorMsg = $request->input('transfer_record.fail_reason');
                switch ($errorMsg)
                {
                    case "1":
                        $transaction_temp->update(['status' => 'REJ', 'errorMsg' => 'The information on the name/account number/card number is incorrect']);
                        break;
                    case "2":
                        $transaction_temp->update(['status' => 'REJ', 'errorMsg' => 'The transaction amount exceeds the daily limit']);
                        break;
                    case "3":
                        $transaction_temp->update(['status' => 'REJ', 'errorMsg' => 'Bank is maintaining']);
                        break;
                    case "4":
                        $transaction_temp->update(['status' => 'REJ', 'errorMsg' => 'An unknown error']);
                        break;
                    case "5":
                        $transaction_temp->update(['status' => 'REJ', 'errorMsg' => 'The information is wrong or the bank encountered an unknown error']);
                        break;
                }   
                return response()->json(['message' => 'Callback successfully!'], 201);
            }
            else {
                return response()->json(['error' => 'Transaction not found or invalid status'], 404);
            }

        } catch (\Exception $e) {
            Log::error('Callback failed: ' . $e->getMessage());
            return response()->json(['error' => 'Callback failed.'], 500);
        }
    }

    public function callAPIPartnerWithdraw ($order_code, $request)
    {
        // Define required parameters
        $partner_id = 'your_partner_id'; // Replace with your partner ID
        $timestamp = Carbon::now()->timestamp; // Current timestamp
        $random = Str::random(10); // Generate a random string
        $partner_order_code = $order_code; // Unique transaction ID
        $amount = $request->input('amount'); // Amount of the payment
        $payee_bank_code = $request->input('payee_bank_code');
        $payee_bank_account_type = 'account';
        $payee_bank_account_no = $request->input('payee_bank_account_no');
        $payee_bank_account_name = $request->input('payee_bank_account_name');
        $notify_url = 'https://yourdomain.com/notify'; // Notification URL
        $message = ''; // Return URL after transaction
        $extra_data = ''; // Additional data if required
        $partner_secret = 'your_partner_secret'; // Replace with your partner secret

        // Generate signature
        $signature_string = implode(':', [
            $partner_id,
            $timestamp,
            $random,
            $partner_order_code,
            $amount,
            $payee_bank_code,
            $payee_bank_account_type,
            $payee_bank_account_no,
            $payee_bank_account_name,
            $message,
            $extra_data,
            $partner_secret
        ]);

        $sign = md5($signature_string);

        // Prepare data for the request
        $data = [
            'partner_id' => $partner_id,
            'timestamp' => $timestamp,
            'random' => $random,
            'partner_order_code' => $partner_order_code,
            'amount' => $amount,
            'payee_bank_code' => $payee_bank_code,
            'payee_bank_account_type' => $payee_bank_account_type,
            'payee_bank_account_no' => $payee_bank_account_no,
            'payee_bank_account_name' => $payee_bank_account_name,
            'notify_url' => $notify_url,
            'message' => $message,
            'extra_data' => $extra_data,
            'sign' => $sign,
        ];

        // Call the API using HTTP POST
        try {
            //$response = Http::post('https://example.com/gateway/bnb/transferATM.do', $data);
            $response = '{
                "code":200, "msg":"success","data":{ "partner_order_code":"2021112116414321046"
                } }';

            // Log response for debugging purposes
            $responseArray = json_decode($response, true);
            Log::info('API Response:', ['response' =>  $responseArray]);

            // Return the response or handle it accordingly
            return  $responseArray;
        } catch (\Exception $e) {
            Log::error('API Call Failed:', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
