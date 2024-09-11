<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampainFX_Txn;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DepositManageAPIController extends Controller
{
    
    public function createOrder(Request $request)
    {
        try {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'amount' => 'required|string|max:255',
                'customer_id' => 'required|integer|min:1',
                'campaign_id' => 'required|integer|min:1',
                'ewallet_adress_campain' => 'required|string|max:500',
                'description' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order_code = 'ORD' . Str::uuid()->toString();
            $customerByID = Customer::find($request->input('customer_id'));
            // Create a new order
            $campaign_txn = CampainFX_Txn::create([
                'campainID' => $request->input('campaign_id'),
                'customerID' => $request->input('customer_id'),
                'ewalletAdressCampain' => $request->input('ewallet_adress_campain'),
                'txnType' => 'DEPOSIT',
                'amount' => $request->input('amount'),
                'transactionHash' => $order_code,
                'txnDescription' => $request->input('description'),
                'status' => 'ORIG'
            ]);

            $createOrder = self::callAPIPartnerVA($order_code, $customerByID->full_name, $request);
            if($createOrder['code'] = 200){
                $campaign_txn->update(['status' => 'WAIT'
                                        ,'transactionHashPartner' => $createOrder['data']['system_order_code']
                                        ,'paymentID' => $createOrder['data']['payment_id']
                ]);
                // Return a success response with the created order
                return response()->json(['order' => $createOrder, 'message' => 'Order created successfully!'], 201);
            }

            return response()->json(['error' => 'Create Order Deposit failed.'], 500);

        } catch (\Exception $e) {
            Log::error('Create Order Deposit failed: ' . $e->getMessage());
            return response()->json(['error' => 'Create Order Deposit failed.'], 500);
        }
    }

    public function callbackDeposit(Request $request)
    {
        try {
            $CampainFXTXN = CampainFX_Txn::where('transactionHashPartner', $request->input('system_order_code'))
                                        ->where('transactionHash', $request->input('partner_order_code'))
                                        ->where('paymentID', $request->input('payment.payment_id'))
                                        ->first();  // Use first() to get a single record
    
            $status = $request->input('payment.status');
        
            if ($status == "4" && $CampainFXTXN) {
                // Update the record if it exists
                $CampainFXTXN->update(['status' => 'DONE']);
                return response()->json(['message' => 'Callback successfully!'], 201);
            } else {
                return response()->json(['error' => 'Transaction not found or invalid status'], 404);
            }

        } catch (\Exception $e) {
            Log::error('Callback failed: ' . $e->getMessage());
            return response()->json(['error' => 'Callback failed.'], 500);
        }
    }

    public function callAPIPartnerVA ($order_code, $name, $request)
    {
        // Define required parameters
        $partner_id = 'your_partner_id'; // Replace with your partner ID
        $timestamp = Carbon::now()->timestamp; // Current timestamp
        $random = Str::random(10); // Generate a random string
        $partner_order_code = $order_code; // Unique transaction ID
        $amount = $request->input('amount'); // Amount of the payment
        $customer_name = $name; // Example customer name
        $payee_name = ''; // Leave empty if not required
        $notify_url = 'https://yourdomain.com/notify'; // Notification URL
        $return_url = 'https://yourdomain.com/return'; // Return URL after transaction
        $extra_data = ''; // Additional data if required
        $partner_secret = 'your_partner_secret'; // Replace with your partner secret

        // Generate signature
        $signature_string = implode(':', [
            $partner_id,
            $timestamp,
            $random,
            $partner_order_code,
            $amount,
            $customer_name,
            $payee_name,
            $notify_url,
            $return_url,
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
            'customer_name' => $customer_name,
            'payee_name' => $payee_name,
            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'extra_data' => $extra_data,
            'sign' => $sign,
        ];

        // Call the API using HTTP POST
        try {
            //$response = Http::post('https://example.com/gateway/bnb/createVA.do', $data);
            $response = '{
                "code":200, "msg":"success", "data":{
                "partner_id":"10081", "system_order_code":"VA2021112123425295326442X8G", "partner_order_code":"2021112123425178828", "amount":4000000000,
                                  
                "request_time":1637512972, "bank_account":{
                "bank_code":"VCCB", "bank_name":"VIETCAPITALBANK", "bank_account_no":"99900030002819", "bank_account_name":"AP DTD"
                },
                "payment_id":"361930bd-86bb-4ac7-adbb-023d6ef22137", "payment_url":"https://vi.long77.net/gateway/pay/paymentBnBVA.do?id=VA2021112123425295326442X8G"
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
