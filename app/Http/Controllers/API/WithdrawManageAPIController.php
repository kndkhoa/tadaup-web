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
                'customer_id' => 'required|string|min:1',
                'amount' => 'required|string|max:255',
                //'eWallet' => 'required|string|max:500',
               // 'description' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $order_code = 'ORD' . Str::uuid()->toString();
            $customerByID = Customer::find($request->input('customer_id'));
            // Create a new order
            $transaction_temp = transaction_temp::create([
                'user_id' => $request->customer_id,
                'type' => 'WITHDRAW',
                'amount' => $request->amount,
                'currency' => 'USD',
                'eWallet' => $request->eWallet ?? null, 
                'transactionHash' => $order_code ?? null,
                'description' => $request->description ?? null,
                'status' => 'WAIT'

            ]);

            return response()->json(['order' => $order_code, 'message' => 'Withdraw created successfully!'], 201);

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

    
}
