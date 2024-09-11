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

    public function approve($id)
    {
        try {
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            
            if (!$customer) {
                return redirect()->back()->withErrors('Customer not found.');
            }

            $desired_status = 'DONE';
            
            // Debugging (this will stop execution, remove or comment out after debugging)
            // dd($customer['full_name']);

            $transaction_temp = Transaction_Temp::findOrFail($id);
            
            // Update the transaction status and set the originating person
            $transaction_temp->update([
                'status' => $desired_status,
                'origPerson' => $customer->full_name
            ]);

            // If you need to record the transaction, uncomment and implement the method
            // Transaction::recordTransaction($transaction_temp->user_id, 'WITHDRAW', $transaction_temp->amount);

            return redirect()->back()->with('success', 'Transaction status approved and updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('withdrawTransaction')
                ->withErrors('Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function reject($id)
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
                'origPerson' => $customer->full_name
            ]);
            
            return redirect()->back()->with('success', 'Transaction status reject updated successfully.');
        }
        catch (e){
            return redirect()->route('withdrawTransaction')
            ->withErrors('You have fail reject transaction!');
        }
    }


}