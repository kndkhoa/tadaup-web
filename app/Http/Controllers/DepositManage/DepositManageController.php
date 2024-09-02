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
            $CampainFXTXN_ID = CampainFX_Txn::where('CampainFX_TXN.campainID', $campainID)
                            ->where('CampainFX_TXN.txnType', 'DEPOSIT')
                            ->where('CampainFX_TXN.status' , 'WAIT')
                            ->orderBy('CampainFX_TXN.created_at', 'desc') // Sort by creation date in descending order
                            ->join('customers', 'CampainFX_TXN.customerID', '=', 'customers.user_id')
                            ->select('CampainFX_TXN.*', 'customers.full_name as customer_name') 
                            ->get();
            $data = compact('CampainFX_ID','CampainFXTXN_ID');
            return view('depositManage.depositmanage', $data);
        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    public function showDepositHistory()
    {
        try{
            $desired_status = ['APPR', 'REJ'];
            $type = 'DEPOSIT';
            $CampainFXTXN = CampainFX_Txn::whereIn('CampainFX_TXN.status', $desired_status)
                                ->where('CampainFX_TXN.txnType', $type)
                                ->orderBy('CampainFX_TXN.created_at', 'desc') // Sort by creation date in descending order
                                ->join('customers', 'CampainFX_TXN.customerID', '=', 'customers.user_id')
                                ->join('campainFX', 'CampainFX_TXN.campainID', '=', 'campainFX.campainID')
                                ->select('CampainFX_TXN.*', 'customers.full_name as customer_name', 'campainFX.campainName') // Select relevant columns
                                ->get();
            return view('depositManage.deposithistory', compact(['CampainFXTXN']));

        }
        catch (e){
            return redirect()->route('campaignTransaction')
            ->withErrors('Get All Campaign Fail.' + e);
        }
    }

    public function depositApprove($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id );
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            if($CampainFXTXN_ID->status == 'WAIT')
            {
                $CampainFXTXN_ID->update(['status' => 'APPR',
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
            $customer = Customer::find($user_id );
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
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

}