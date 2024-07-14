<?php

// Add this line at the top of your BankController.php file


namespace App\Http\Controllers\Transaction;

use App\Models\Customer;
use App\Models\Transaction_Temp;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;



class TransactionController extends Controller
{
    

    /**
     * Display Transaction History.
     * @return \Illuminate\Http\Response
     */

    public function transactionHistory()
    {
        $user_id = Auth::user()->user_id;
        $customer = Customer::find($user_id );
        $desired_status_wait = 'WAIT';
        $desired_status_rej = 'REJ';
        $user = User::find($user_id ); // Xử lý dữ liệu user
        //$transactions = Transaction::find($user_id );
        $transactions = Transaction::where('user_id', $user_id)
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
        $transactions_temp = Transaction_Temp::where('user_id', $user_id)
                            ->whereIn('status', [$desired_status_wait, $desired_status_rej])
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
        return view('transaction.transaction', compact(['customer','user', 'transactions', 'transactions_temp']));
    }


    ////////////////////////////////////////////////Calculate Commission//////////////////////////////////////////////
    /**
     * Display Calculate Commission.
     * @return \Illuminate\Http\Response
     */
    public function calculateCommission()
    {
        $user_id = Auth::user()->user_id;

        $customers = Customer::getAll();
        return view('transaction.calculatecommision', compact(['customers']));
    }

     /**
     * Post Calculate commission
     * @return \Illuminate\Http\Response
     */

    public function calculate(Request $request)
    {
        // Assuming you have the user's ID from authentication context
        $user_id = $request->user_id;
        if(!$user_id){
            return back()->withErrors(['user_id' => 'Please select Fullname']);
        }
        // Get top-level customers for the user (those without a parent)
        $customers = Customer::where('user_id', $user_id)
                             ->get();
        $user_sponser_id =  $customers[0]->user_sponser_id;
        $collection = collect($customers);
        $i = 1;
        while ($i < 5){
            
            $customersParent = Customer::where('user_id', $user_sponser_id)
                             ->get();
            if($customersParent->isEmpty()){
                break;
            }
            $user_sponser_id =  $customersParent[0]->user_sponser_id;
            $collection->push($customersParent[0]);
            $i++;
        }
        self::calComission($collection->all(),$request);
        return redirect()->route('transaction-history')
            ->with('success','You have successfully calculate commmission!');
    }

    public function calComission($collections, $request)
    {
        $amount = $request->amount;
        $levels = [0.50, 0.30, 0.10, 0.05, 0.05];
        $i =0; 
        $count = count($collections);
        foreach ($collections as $collection) {
            // Check if 'user_id' exists in the collection array
            if (isset($collection['user_id'])) {
                $userId = $collection['user_id'];
                $amountCommission = $amount * $levels[$i];
                Transaction::recordTransaction($userId, 'DEPOSIT', round($amountCommission, 2));
                $i ++;
            } else {
                // Handle the case where 'user_id' is not set
                error_log('User ID not found in collection');
                // Optionally, continue to the next iteration or implement additional error handling
            }
        }
        while($i < 5){
            $amountCommission = $amount * $levels[$i];
            Transaction::recordTransaction(1, 'DEPOSIT', round($amountCommission, 2));
            $i++;
        }
    }

    // public function calculateCommissions($amount, $levels = [0.50, 0.30, 0.10, 0.05, 0.05])
    // {
    //     $commissions = [];
    //     $currentLevel = 0;
    //     $this->recurseCommissions($commissions, $amount, $levels, $currentLevel);
    //     return $commissions;
    // }

    // private function recurseCommissions(&$commissions, $amount, $levels, $currentLevel)
    // {
    //     if ($currentLevel < count($levels)) {
    //         $commissions[$currentLevel] = $amount * $levels[$currentLevel];
    //         foreach ($this->children as $child) {
    //             $child->recurseCommissions($commissions, $amount, $levels, $currentLevel + 1);
    //         }
    //     }
    // }

    /////////////////////////////////////////////////Withdraw Commission//////////////////////////////////////////////
    /**
     * Display Withdraw Commission.
     * @return \Illuminate\Http\Response
     */

    public function withdrawCommission()
    {
        $user_id = Auth::user()->user_id;

        $customer = Customer::find($user_id );
        $user = User::find($user_id ); // Xử lý dữ liệu user
        $transactions = Transaction::where('user_id', $user_id)
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
        $response = Http::get('https://api.vietqr.io/v2/banks');
        $banks = $response->json()['data'];
        return view('transaction.withdrawcommision', compact(['customer','user', 'transactions', 'banks']));
    }

     /**
     * Post WithDraw Commission.
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request)
    {
        try{
            $user_id = Auth::user()->user_id;
            if(!$request->bankList){
                return back()->withErrors(['bank_name' => 'Please select Bank Name']);
            }
            Transaction_Temp::recordTransaction($user_id, $request, 'WITHDRAW', 'WAIT', 'USD');
            return redirect()->route('transaction-history')
            ->with('success','You have successfully withdraw commmission!');
        }
        catch (e){
            return redirect()->route('withdraw-commission')
            ->withSuccess('You have fail withdraw commmission!');
        }
    }



    /////////////////////////////////////////////////Approve Withdraw Commission//////////////////////////////////////////////
     /**
     * Display Approve Commission.
     * @return \Illuminate\Http\Response
     */
    public function approveCommission()
    {
        $desired_status = 'WAIT';
        $transactions_temp = Transaction_Temp::where('status', $desired_status)
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
        return view('transaction.approvetransaction', compact(['transactions_temp']));
    }

    public function approve($id)
    {
        try{
            $desired_status = 'APPR';
            $transaction_temp_id = Transaction_Temp::findOrFail($id);
            $transaction_temp_id->update(['status' => $desired_status]);
            Transaction::recordTransaction($transaction_temp_id->user_id, 'WITHDRAW', $transaction_temp_id->amount);
            return redirect()->back()->with('success', 'Transaction status approve updated successfully.');
        }
        catch (e){
            return redirect()->route('transaction-history')
            ->withErrors('You have fail approve commmission!');
        }
        
    }

    public function reject($id)
    {
        try{
            $desired_status = 'REJ';
            $transaction_temp_id = Transaction_Temp::findOrFail($id);
            $transaction_temp_id->update(['status' => $desired_status]);
            return redirect()->back()->with('success', 'Transaction status reject updated successfully.');
        }
        catch (e){
            return redirect()->route('transaction-history')
            ->withErrors('You have fail reject commmission!');
        }
    }
    

}