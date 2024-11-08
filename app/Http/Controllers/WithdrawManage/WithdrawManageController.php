<?php

namespace App\Http\Controllers\WithdrawManage;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Transaction_Temp;
use App\Models\CustomerItem;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTadaup;


class WithdrawManageController extends Controller
{
    

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */

    

    public function showWithDrawList()
    {
        $desired_status = 'WAIT';
        $type = 'WITHDRAW';
        $transactions_temp = Transaction_Temp::where('status', $desired_status)
                            ->where('type', $type)
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->join('customers', 'transactions_temp.user_id', '=', 'customers.user_id')
                            ->select('transactions_temp.*', 'customers.full_name as customer_name',) // Select relevant columns
                            ->get();
        return view('withdrawManage.withdrawtransaction', compact(['transactions_temp']));
    }

    public function showWithDrawHistory()
    {
        $desired_status = ['DONE', 'REJ'];
        $type = 'WITHDRAW';
        $transactions_temp = Transaction_Temp::whereIn('status', $desired_status)
                            ->where('type', $type)
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->join('customers', 'transactions_temp.user_id', '=', 'customers.user_id')
                            ->select('transactions_temp.*', 'customers.full_name as customer_name',) // Select relevant columns
                            ->get();
        return view('withdrawManage.withdrawhistory', compact(['transactions_temp']));
    }

    public function approve($id, Request $request)
    {
        try {
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            
            if (!$customer) {
                return redirect()->back()->withErrors('Customer not found.');
            }

            $transaction_temp = Transaction_Temp::findOrFail($id);
            
            DB::transaction(function () use ($transaction_temp, $customer, $request) {
                $desired_status = 'DONE';
                // Update the transaction status and set the originating person
                $transaction_temp->update([
                    'status' => $desired_status,
                    'origPerson' => $customer->full_name,
                    'transactionHash' => $request->hash
                ]);

                //Vi USDT khach
                $customerItemType1 = CustomerItem::where('customer_id', $transaction_temp->user_id)
                                            ->where('type', 1)
                                            ->firstOrFail();
                $customerItemType1->decrement('value', (double) $transaction_temp->amount);

                //Vi INCOME Tada
                $wallet_tada =  WalletTadaup::where('walletName', 'LIQUID')
                                    ->where('id', 2)->first();
                $wallet_tada->decrement('value', (double) $transaction_temp->amount);

            });

            return redirect()->back()->with('success', 'Transaction status approved and updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('showWithDrawList')
                ->withErrors('Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function reject($id, Request $request)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id );

            if (!$customer) {
                return redirect()->back()->withErrors('Customer not found.');
            }

            $desired_status = 'REJ';
            $transaction_temp = Transaction_Temp::findOrFail($id);
           
            // Update the transaction status and set the originating person
            $transaction_temp->update([
                'status' => $desired_status,
                'origPerson' => $customer->full_name,
                'errorMsg' => $request->reject
            ]);
            
            return redirect()->back()->with('success', 'Transaction status reject updated successfully.');
        }
        catch (e){
            return redirect()->route('withdrawTransaction')
            ->withErrors('You have fail reject transaction!');
        }
    }


}