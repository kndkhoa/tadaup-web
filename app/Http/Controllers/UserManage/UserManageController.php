<?php

namespace App\Http\Controllers\UserManage;

use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerConnection;
use App\Models\CampainFX_Txn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTadaup;
use Illuminate\Support\Facades\Http;  // Laravel's HTTP client
use App\Models\Transaction_Temp;
use App\Models\CustomerItem;
use App\Models\CustomerReport;


class UserManageController extends Controller
{
    

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */

    

    public function showCustomerList()
    {
        try{
            //$customers = Customer::all();
            $customers = Customer::join('users', 'customers.customer_id', '=', 'users.user_id')
                            ->select('customers.*',  'users.email') 
                            ->get();
            return view('usermanage.customer-list', compact(['customers']));
        }
        catch (e){
            return redirect()->route('showCustomerList')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    public function showWalletTada()
    {
        try{
            //$customers = Customer::all();
            $walletTadaups = WalletTadaup::getAll();
            return view('usermanage.wallettada-list', compact(['walletTadaups']));
        }
        catch (e){
            return redirect()->route('showWalletTada')
            ->withErrors('Get All Wallet Fail.' + e);
        }
    }

    public function showWalletTadaHistory()
    {
        try{
            $walletTadaupHist = transaction_temp::where('user_id', '1')
                                                ->where('eWallet', '2')
                                        ->get(); 
            return view('usermanage.wallettada-history', compact(['walletTadaupHist']));
        }
        catch (e){
            return redirect()->route('showWalletTadaHistory')
            ->withErrors('Get All Wallet Fail.' + e);
        }
    }

    public function calculatePoint()
    {
        // API URL
        $url = 'http://admin.tducoin.com/api/usermanage/calculate-point';
        
        // Make the API call using POST method with the API key in headers
        $response = Http::withHeaders([
            'x-api-key' => 'oqKbBxKcEn9l4IXE4EqS2sgNzXPFvE',
        ])->post($url, []);

        // Check the status and return the result
        if ($response->successful()) {
            // The API call was successful, return or process the response
            $data = $response->json(); // Assuming the response is JSON
            return redirect()->back()->with('success', 'Calculate point successfully.');
        } else {
            // The API call failed, return error response
            return redirect()->back()->withErrors('Calculate point fail.');
        }
    }

    public function depositWalletTadaIncome(Request $request)
    {
        try{
            if(!$request->amount){
                return back()->withErrors(['amount' => 'Please fill amount']);
            }
            $wallet_tada =  WalletTadaup::where('walletName', 'INCOME')
                        ->where('id', 1)->first();
            $wallet_tada->increment('value', (double) $request->amount);
            return redirect()->back()->with('success', 'Deposit amount to wallet Income successfully.');
            
        }
        catch (e){
            return redirect()->back()->withErrors('Deposit amount to wallet Incomefail.');
        }
    }

    public function showCustomerDetail($cutomerID)
    {
        try{
            $customerByID = Customer::find($cutomerID);

            $Customer_connection = Customer::where('customers.customer_id', $cutomerID)
                            ->join('cutomer_connection', 'customers.customer_id', '=', 'cutomer_connection.customer_id')
                            ->where('cutomer_connection.status', 'ACTIVE')
                            ->select('cutomer_connection.*', 'customers.full_name as customer_name', 'customers.phone as phone') 
                            ->get();

            $customer_items = CustomerItem::where('customer_id', $cutomerID)
                            ->join('item', 'customer_item.type', '=', 'item.id')
                            ->select('customer_item.*', 'item.description as description') 
                            ->get();

            $campainFX_Txns = CampainFX_Txn::where('customerID', $cutomerID)
                            ->where('status', 'WAIT')
                            ->where('txnType', 'DEPOSIT')
                            ->whereNotNull('transactionHash')
                            ->get();
                
            $transaction_temps = transaction_temp::where('user_id', $cutomerID)
                                                ->orderBy('created_at', 'desc')
                                                ->get(); 
            //Tree view
            $userTree = Customer::with('children')->find($cutomerID);
            if (!$userTree) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $tree = $this->buildTree($userTree);

            $data = compact('Customer_connection', 'customerByID', 'tree', 'campainFX_Txns', 'customer_items', 'transaction_temps');
            return view('usermanage.customer-detail', $data);
        }
        catch (e){
            return redirect()->route('showCustomerList')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    private function buildTree($user)
    {
        $tree = [
            'id' => $user->user_id,
            'text' => $user->full_name,
            'children' => []
        ];

        foreach ($user->children as $child) {
            $tree['children'][] = $this->buildTree($child);
        }

        return $tree;
    }

    public function creatConnection(Request $request)
    {
        try{
            DB::transaction(function () use ($request) {
                CustomerConnection::addRecord($request);

                $campainFXTXN_ID = CampainFX_Txn::where('customerID', $request->customerid)
                                ->where('txnType', 'DEPOSIT')
                                ->where('transactionHash', $request->txnhash)
                                ->first();
                $desired_status = $campainFXTXN_ID->status ?? '';
                if($campainFXTXN_ID->status == 'WAIT')
                {
                    $campainFXTXN_ID->update(['status' => 'PROCESS'
                                            ]);
                }
            });
            return redirect()->route('showCustomerDetail', $request->customerid)
                ->withSuccess('Add connection successfully.');
        }
        catch (e){
            return redirect()->route('showCustomerDetail', $request->customerid)
            ->withErrors('Add connection fail.' + e);
        }
    }

    public function deleteConnection($id)
    {
        try{
            $customerConnection = CustomerConnection::findOrFail($id);
            $customerConnection->update(['status' => 'INACTIVE'
                                        ]);
            return redirect()->back()->with('success', 'Customer Connection deleted successfully.');
        }
        catch (e){
            return redirect()->back()->withErrors('Customer Connection deleted fail.');
        }
    }

    //==============Share Commission MLML===================//
    public function showCommissionMLM()
    {
        try{
            $customers = Customer::getAll();
            $transaction_temp_mlm = Transaction_Temp::where('origPerson', 'MLM')
                                        ->orderBy('created_at', 'desc') // Order by created_at in descending order
                                        ->take(20) // Limit the results to 20
                                        ->join('customers', 'transactions_temp.user_id', '=', 'customers.user_id')
                                        ->select('transactions_temp.*', 'customers.full_name as customer_name') 
                                        ->get();
            $data = compact('customers', 'transaction_temp_mlm');
            return view('usermanage.commission-mlm', $data);
        }
        catch (e){
            return redirect()->route('showCustomerList')
            ->withErrors('Show Commission Fail.' + e);
        }
    }

    public function calculateMLM($id)
    {
        $detail = CampainFX_Txn::where('id', $id)
                                        ->first(); 
        // Assuming you have the user's ID from authentication context
        $customer_id = $detail->customerID;
        if(!$customer_id){
            return back()->withErrors(['user_id' => 'Please select Fullname']);
        }
        if(!$detail->amount){
            return back()->withErrors(['amount' => 'Please select amount']);
        }

        // Get top-level customers for the user (those without a parent)
        $customers = Customer::where('user_id', $customer_id)
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
        self::calComission($collection->all(),$detail);
        return redirect()->route('showDepositWin')
            ->with('success','You have successfully calculate commmission!');
    }

    public function calComission($collections, $request)
    {
        $amount = $request->amount * 0.05;
        $levels = [0.50, 0.20, 0.10, 0.05, 0.05, 0.1];
        $i =0; 
        $count = count($collections);
        foreach ($collections as $collection) {
            // Check if 'user_id' exists in the collection array
            if (isset($collection['user_id'])) {
                $userId = $collection['user_id'];
                $amountCommission = $amount * $levels[$i];
                $this->createTransaction($userId, round($amountCommission, 2), $request);
                $i ++;
            } else {
                // Handle the case where 'user_id' is not set
                error_log('User ID not found in collection');
                // Optionally, continue to the next iteration or implement additional error handling
            }
        }
        while($i < 5){
            $amountCommission = $amount * $levels[$i];
            $this->createTransaction('1', round($amountCommission, 2), $request);
            $i++;
        }
        if($i===5){
            $amountCommission = $amount * $levels[$i];
            $this->createTransaction('1', round($amountCommission, 2), $request);
        }
        
    }

     // Store transaction temporarily
    private function createTransaction($userId, $amount, $request)
    {
        DB::transaction(function () use ($userId, $amount, $request) {
            $order_code = 'ORD' . Str::uuid()->toString();

            $customerItemType6 = CustomerItem::where('customer_id', $userId)
                        ->where('type', 6)
                        ->firstOrFail(); 

            // Increment the value for type = 2
            $customerItemType6->update([
                'value' => (double) $customerItemType6->value + (double) $amount
            ]);

            Transaction_Temp::create([
                'user_id' => $userId,
                'type' => 'DEPOSIT',
                'amount' => $amount,
                'currency' => 'USDT',
                'transactionHash' => $order_code,
                'status' => 'DONE',
                'eWallet' => '6',
                'description' => 'Share Commission MLM from customer ID: ' .$request->customerID,
                'origPerson' => 'MLM'
            ]);
        });
    }
  
    //==============Share Report Trading===================//
    public function showReportTrading(Request $request, $id = null)
    {
        try {
            // Retrieve all customers for the dropdown
            $customers = Customer::all();
            $id = $request->query('id', $id);
            if ($id) {
                // Retrieve a single report by customer ID
                $customerReport = CustomerReport::where('customer_report.customer_id', $id)
                    ->join('customers', 'customer_report.customer_id', '=', 'customers.customer_id')
                    ->select('customer_report.*', 'customers.full_name as customer_name')
                    ->orderBy('customer_report.created_at', 'desc') // Order by created_at in descending order
                    ->get();

                $data = compact('customerReport', 'customers');
                return view('usermanage.report-trading', $data);
            }

            // Retrieve all reports
            $customerReport = CustomerReport::join('customers', 'customer_report.customer_id', '=', 'customers.customer_id')
                ->select('customer_report.*', 'customers.full_name as customer_name')
                ->orderBy('customer_report.created_at', 'desc') // Order by created_at in descending order
                ->get();

            $data = compact('customerReport', 'customers');
            return view('usermanage.report-trading', $data);
        } catch (\Exception $e) {
            return redirect()->route('showReportTrading')
                ->withErrors('Show Report Trading failed: ' . $e->getMessage());
        }
    }


}