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

    public function showCustomerDetail($cutomerID)
    {
        try{
            $customerByID = Customer::find($cutomerID);

            $Customer_connection = Customer::where('customers.customer_id', $cutomerID)
                            ->join('cutomer_connection', 'customers.customer_id', '=', 'cutomer_connection.customer_id')
                            ->where('cutomer_connection.status', 'ACTIVE')
                            ->select('cutomer_connection.*', 'customers.full_name as customer_name', 'customers.phone as phone') 
                            ->get();

            $campainFX_Txns = CampainFX_Txn::where('customerID', $cutomerID)
                            ->where('status', 'WAIT')
                            ->where('txnType', 'DEPOSIT')
                            ->whereNotNull('transactionHash')
                            ->get();
            //Tree view
            $userTree = Customer::with('children')->find($cutomerID);
            if (!$userTree) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $tree = $this->buildTree($userTree);

            $data = compact('Customer_connection', 'customerByID', 'tree', 'campainFX_Txns');
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

    
  

}