<?php

// Add this line at the top of your BankController.php file


namespace App\Http\Controllers\Campain;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use App\Models\CampainFX;
use App\Models\CampainFX_Txn;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;



class CampainFXTXNController extends Controller
{
    

    /**
     * Display Campain Editor.
     * @return \Illuminate\Http\Response
     */

    public function transactionDetail($campainID)
    {
        $CampainFX_ID = CampainFX::findOrFail($campainID);
        $CampainFXTXN_ID = collect(); // Initialize as an empty collection
        $CampainFXTXN_ID_Done = collect(); // Initialize as an empty collection
        if($CampainFX_ID-> status == 'ORIG')
        {
            $CampainFXTXN_ID = CampainFX_Txn::where('campainID', $campainID)
                        ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                        ->get();
        }
        elseif($CampainFX_ID-> status == 'DONE')
        {
            $CampainFXTXN_ID = CampainFX_Txn::where('campainID', $campainID)
                            ->where('status', 'Y')
                            ->where('txnType', 'DEPOSIT')
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
            $CampainFXTXN_ID_Done = CampainFX_Txn::where('campainID', $campainID)
                            ->where('txnType', 'WITHDRAW')
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
        }
        $sumsAmount = CampainFX_TXN::select('campainID', \DB::raw('SUM(amount) as total_amount'), \DB::raw('SUM(percent) as total_percent'))
            ->where('campainID', $CampainFX_ID->campainID)
            ->where('status', 'Y')
            ->groupBy('campainID')
            ->get();
        $data = compact('CampainFX_ID','CampainFXTXN_ID', 'sumsAmount','CampainFXTXN_ID_Done');
        return view('campain.campain-transaction-detail', $data);
    }

    public function approve($id)
    {
        try{
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            if($CampainFXTXN_ID->status == 'N')
            {
                $CampainFXTXN_ID->update(['status' => 'Y']);
            }
             return redirect()->back()->with('success', 'Transaction approve successfully.');
        }
        catch (e){
            return redirect()->route('campainFXTXN.campain-transaction-detail')
            ->withErrors('Transaction approve fail.');
        }
    }

    public function reject($id)
    {
        try{
            $CampainFXTXN_ID = CampainFX_Txn::findOrFail($id);
            $desired_status = $CampainFXTXN_ID->status ?? '';
            $CampainFXTXN_ID->update(['status' => 'R']);
            return redirect()->back()->with('success', 'Transaction reject successfully.');
        }
        catch (e){
            return redirect()->route('campainFXTXN.campain-transaction-detail')
            ->withErrors('Transaction reject fail.');
        }
    }
    
    public function submitPayment($campainID)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($campainID);
            $profitAmount =  $CampainFX_ID->profitAmount ?? 0;
            $CampainFXTXN_ID = CampainFX_Txn::where('campainID', $campainID)
                            ->where('status', 'Y')
                            ->orderBy('created_at', 'desc') // Sort by creation date in descending order
                            ->get();
            if($profitAmount > 0){
                foreach($CampainFXTXN_ID as $campainFXTXN){
                    $amount = ($profitAmount * $campainFXTXN->percent)/100;
                    $roundedAmount = round($amount, 2); 
                    CampainFX_Txn::submitPayment($campainFXTXN, $roundedAmount, 'WITHDRAW');
                }
            }
            return redirect()->back()->with('success', 'Submit Payment successfully.');
        }
        catch (e){
            return redirect()->route('campainFXTXN.campain-transaction-detail')
            ->withErrors('Submit Payment fail.');
        }
    }

}