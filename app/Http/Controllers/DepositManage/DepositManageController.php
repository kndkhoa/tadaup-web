<?php

namespace App\Http\Controllers\DepositManage;

use App\Models\Customer;
use App\Models\User;
use App\Models\CampainFX;
use App\Models\CampainFX_Txn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Transaction_Temp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WalletTadaup;
use App\Models\CustomerItem;
use Illuminate\Support\Facades\DB;


class DepositManageController extends Controller
{
    

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */

    

    // public function showDepositList()
    // {
    //     $desired_status = 'WAIT';
    //     $type = 'DEPOSIT';
    //     $transactions_temp = Transaction_Temp::where('status', $desired_status)
    //                         ->where('type', $type)
    //                         ->orderBy('created_at', 'desc') // Sort by creation date in descending order
    //                         ->join('customers', 'Transactions_Temp.user_id', '=', 'customers.user_id')
    //                         ->select('Transactions_Temp.*', 'customers.full_name as customer_name',) // Select relevant columns
    //                         ->get();
    //     return view('depositManage.deposittransaction', compact(['transactions_temp']));
    // }

    public function showCampaignList()
    {
        try{
            $campaigns = CampainFX::getAll();
            return view('depositManage.campaigntransaction',  compact(['campaigns']));
        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    public function depositDetail($campainID)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($campainID);
            $CampainFXTXN_ID = CampainFX_TXN::where('campainFX_Txn.campainID', $campainID)
                            ->where('campainFX_Txn.txnType', 'DEPOSIT')
                            ->where('campainFX_Txn.status' , 'WAIT')
                            ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                            ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.user_id')
                            ->select('campainFX_Txn.*', 'customers.full_name as customer_name') 
                            ->get();
            $data = compact('CampainFX_ID','CampainFXTXN_ID');
            return view('depositManage.depositdetail', $data);
        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    public function registerFund($campainID)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($campainID);
            $customers = Customer::getAll();
            $data = compact('CampainFX_ID', 'customers');
            return view('depositManage.registerfund', $data);
        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get Campaign ' .$campainID. 'Fail.' + e);
        }
    }

    public function registerFundByID(Request $request)
    {
        try {
            // Assuming you have the user's ID from authentication context
            $customer_id = $request->customer_id;
            if(!$customer_id){
                return back()->withErrors(['customer_id' => 'Please select Fullname']);
            }
            if(!$request->amount){
                return back()->withErrors(['amount' => 'Please select Amount']);
            }

            DB::transaction(function () use ($request) {
                // Create a new order code for deposit
                $order_code = 'ORD' . Str::uuid()->toString();

                // Store transaction temporarily
                $transaction_Temp = Transaction_Temp::create([
                    'user_id' => $request->customer_id,
                    'type' => 'WITHDRAW',
                    'amount' => $request->amount,
                    'currency' => 'USD',
                    'eWallet' => '1',
                    'transactionHash' => $order_code ?? null,
                    'status' => 'DONE',
                ]);

                // Retrieve and update customer item for type = 1
                $customerItemType1 = CustomerItem::where('customer_id', $request->customer_id)
                    ->where('type', 1)
                    ->firstOrFail();

                // Check if the customer has enough funds
                if ((double)$customerItemType1->value < (double)$request->amount) {
                    // Throw an exception if funds are insufficient
                    throw new \Exception('Insufficient funds for customer ' . $request->customer_id);
                }

                // Decrement the value for type = 1
                $customerItemType1->update([
                    'value' => (double) $customerItemType1->value - (double) $request->amount
                ]);

                // Retrieve and update customer item for type = 2
                $customerItemType2 = CustomerItem::where('customer_id', $request->customer_id)
                    ->where('type', 2)
                    ->firstOrFail();

                // Increment the value for type = 2
                $customerItemType2->update([
                    'value' => (double) $customerItemType2->value + (double) $request->amount
                ]);

                // Create a new campaign transaction
                CampainFX_Txn::create([
                    'campainID' => $request->campaign_id,
                    'customerID' => $request->customer_id,
                    'ewalletCustomerID' => $request->ewallet ?? null,
                    'txnType' => 'DEPOSIT',
                    'amount' => $request->amount,
                    'txnDescription' => $request->description ?? null,
                    'transactionHash' => $order_code ?? null,
                    'status' => 'DONE'
                ]);
            });

            return redirect()->route('showDepositDone', $request->customer_id)
                ->withSuccess('Register fund for ' . $request->customer_id . ' successfully.');
        } 
        catch (QueryException $qe) {
            // Log the detailed SQL error for developers
            Log::error('SQL error occurred: ' . $qe->getMessage());
    
            // Return a generic error message to the user
            return redirect()->back()->withErrors('There was an issue processing your request. Please try again later.');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error occurred while registering fund: ' . $e->getMessage());
    
            // Return a user-friendly error message
            return redirect()->back()->withErrors('An error occurred while processing your request. Please check your input and try again.');
        }
    }


    public function showDepositWait()
    {
        try{
            $desired_status = 'WAIT';
            $type = 'DEPOSIT';
            $transactions_temps = Transaction_Temp::where('status', $desired_status)
                                ->where('type', $type)
                                ->orderBy('Transactions_Temp.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'Transactions_Temp.user_id', '=', 'customers.user_id')
                                ->select('Transactions_Temp.*', 'customers.*',) // Select relevant columns
                                ->get();
            return view('depositManage.depositwait', compact(['transactions_temps']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Transaction Fail.' + e);
        }
    }

    public function showDepositDone()
    {
        try{
            $desired_status = ['DONE'];
            $type = 'DEPOSIT';
            $CampainFXTXN = CampainFX_TXN::whereIn('campainFX_Txn.status', $desired_status)
                                ->where('campainFX_Txn.txnType', $type)
                                ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.user_id')
                                ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                ->select('campainFX_Txn.*', 'customers.full_name as customer_name', 'campainFX.campainName', 'campainFX.campainID as campainID') // Select relevant columns
                                ->get();
            return view('depositManage.depositdone', compact(['CampainFXTXN']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Transaction Fail.' + e);
        }
    }

    public function showDepositProcess()
    {
        try{
            $desired_status = ['PROCESS'];
            $type = 'DEPOSIT';
            $CampainFXTXN = CampainFX_TXN::whereIn('campainFX_Txn.status', $desired_status)
                                ->where('campainFX_Txn.txnType', $type)
                                ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.user_id')
                                ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                ->select('campainFX_Txn.*', 'customers.full_name as customer_name', 'campainFX.campainName') // Select relevant columns
                                ->get();
            return view('depositManage.depositprocess', compact(['CampainFXTXN']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Transaction Fail.' + e);
        }
    }

    public function showDepositWin()
    {
        try{
            $desired_status = ['WIN'];
            $type = 'DEPOSIT';
            $CampainFXTXN = CampainFX_TXN::whereIn('campainFX_Txn.status', $desired_status)
                                ->where('campainFX_Txn.txnType', $type)
                                ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.user_id')
                                ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                ->select('campainFX_Txn.*', 'customers.full_name as customer_name', 'campainFX.campainName') // Select relevant columns
                                ->get();
            return view('depositManage.depositwin', compact(['CampainFXTXN']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Transaction Fail.' + e);
        }
    }

    public function showDepositReject()
    {
        try{
            $desired_status = ['REJ'];
            $type = 'DEPOSIT';
            $CampainFXTXN = CampainFX_TXN::whereIn('campainFX_Txn.status', $desired_status)
                                ->where('campainFX_Txn.txnType', $type)
                                ->orderBy('campainFX_Txn.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'campainFX_Txn.customerID', '=', 'customers.user_id')
                                ->join('campainFX', 'campainFX_Txn.campainID', '=', 'campainFX.campainID')
                                ->select('campainFX_Txn.*', 'customers.full_name as customer_name', 'campainFX.campainName') // Select relevant columns
                                ->get();
            return view('depositManage.depositreject', compact(['CampainFXTXN']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Transaction Fail.' + e);
        }
    }

    public function approveWallet($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);

            $transaction_Temp_ID = Transaction_Temp::findOrFail($id);
            $wallet_tada =  WalletTadaup::where('walletName', 'LIQUID')
                            ->where('id', 2)->first();

            $desired_status = $transaction_Temp_ID->status ?? '';
            if($transaction_Temp_ID->status == 'WAIT')
            {
                if($transaction_Temp_ID->transactionHash){
                    $check = $this->checkTransactionByHash($transaction_Temp_ID->transactionHash);
                    if($check){
                        $amountStr = $check[0]['amount_str'];
                        $decimals = $check[0]['decimals'];

                        // Convert amount_str using the token's decimals
                        $amountInDecimals = floatval($amountStr) / pow(10, $decimals);

                        // Format the amount by removing trailing zeros and decimal point if it's a whole number
                        $formattedAmount = (intval($amountInDecimals) == $amountInDecimals) 
                            ? intval($amountInDecimals) 
                            : number_format($amountInDecimals, $decimals);
                        
                        if($check[0]['to_address'] === $wallet_tada->address){ //'TRdPZ3SqzakBGk2HECrUrWe5mtsDHLdFmG'){
                            $transaction_Temp_ID->update(['status' => 'DONE',
                                                'origPerson' => $customer->full_name
                                                ]);
                            
                            
                            $wallet_tada->increment('value', (double) $formattedAmount);

                            if($transaction_Temp_ID->user_id !== "1"){
                                CustomerItem::where('customer_id', $transaction_Temp_ID->user_id)
                                ->where('type', 1)
                                ->increment('value', (double) $formattedAmount);
                            }
                            

                            return redirect()->back()->with('success', 'Transaction approve successfully.');
                        }
                    }
                }
            }
            return redirect()->back()->with('error', 'Transaction approve unSuccessfully.');  
        }
        catch (e){
            return redirect()->route('depositManage.campain-transaction-detail')
            ->withErrors('Transaction approve fail.');
        }
    }


    public function depositApprove($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            $CampainFXTXN_ID = CampainFX_TXN::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            if($CampainFXTXN_ID->status == 'WAIT')
            {
                $CampainFXTXN_ID->update(['status' => 'DONE',
                                        'origPerson' => $customer->full_name
                                        ]);
            }
             return redirect()->back()->with('success', 'Transaction approve successfully.');
        }
        catch (e){
            return redirect()->route('depositManage.campain-transaction-detail')
            ->withErrors('Transaction approve fail.');
        }
    }

    public function depositReject($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            $CampainFXTXN_ID = CampainFX_TXN::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            $CampainFXTXN_ID->update(['status' => 'REJ'
                                    ,'origPerson' => $customer->full_name
                                    ]);
            return redirect()->back()->with('success', 'Transaction reject successfully.');
        }
        catch (e){
            return redirect()->route('campainFXTXN.campain-transaction-detail')
            ->withErrors('Transaction reject fail.');
        }
    }

    public function depositProcess($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            if($CampainFXTXN_ID->status == 'DONE')
            {
                $CampainFXTXN_ID->update(['status' => 'PROCESS',
                                        'origPerson' => $customer->full_name
                                        ]);
            }
             return redirect()->back()->with('success', 'Transaction process successfully.');
        }
        catch (e){
            return redirect()->route('depositManage.campain-transaction-detail')
            ->withErrors('Transaction process fail.');
        }
    }

    public function depositWin($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id);
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            
                $CampainFXTXN_ID->update(['status' => 'WIN',
                                        'origPerson' => $customer->full_name
                                        ]);
            
             return redirect()->back()->with('success', 'Transaction win successfully.');
        }
        catch (e){
            return redirect()->route('depositManage.campain-transaction-detail')
            ->withErrors('Transaction win fail.');
        }
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