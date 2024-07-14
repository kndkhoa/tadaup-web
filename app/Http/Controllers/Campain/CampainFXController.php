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



class CampainFXController extends Controller
{
    

    /**
     * Display Campain Editor.
     * @return \Illuminate\Http\Response
     */

    public function campainEditor(Request $request)
    {
        return view('campain.campain-editor');
    }
    public function save(Request $request)
    {
        try{
            if($request->campainID){
                $CampainFX_ID = CampainFX::findOrFail($request->campainID);
                CampainFX::upsert([
                    'campainID' => $request->campainID,
                    'campainName' => $request->campain_name,
                    'campainDescription' => $request->campain_description,
                    'content' => $request->campain_content,
                    'campain_amount' => $request->campain_amount,
                    'fromDate' => $request->from_date,
                    'toDate' => $request->to_date,
                    'ewalletAddress' => $request->ewallet_address,
                    'network' => $request->ewallet_network,
                    'currency' => 'USDT',
                    'status' => $CampainFX_ID->status,
                    'profitAmount' => $request->profit_amount,
                    'profirPercent' => $request->profit_percent,
                    ],
                    ['campainID'],
                    ['campainName', 'campainDescription', 'content', 'campain_amount', 'fromDate', 'toDate','ewalletAddress','network', 'currency', 'status', 'profitAmount',  'profirPercent']
                );
            }
            else
            {
                CampainFX::upsert([
                    'campainName' => $request->campain_name,
                    'campainDescription' => $request->campain_description,
                    'content' => $request->campain_content,
                    'campain_amount' => $request->campain_amount,
                    'fromDate' => $request->from_date,
                    'toDate' => $request->to_date,
                    'ewalletAddress' => $request->ewallet_address,
                    'network' => $request->ewallet_network,
                    'currency' => 'USDT',
                    'status' => 'ORIG',
                    'profitAmount' => $request->profit_amount,
                    'profirPercent' => $request->profit_percent,
                    ],
                    ['campainID'],
                    ['campainName', 'campainDescription', 'content', 'campain_amount', 'fromDate', 'toDate','ewalletAddress','network', 'currency', 'status', 'profitAmount',  'profirPercent']
                );
            }
            return redirect()->route('campain-new')
                ->withSuccess('Create/Edit campain successfully.');
        }
        catch (e){
            return redirect()->route('campain-list')
            ->withErrors('Create campain fail.' + e);
        }
    }

    /**
     * Display Campain List New.
     * @return \Illuminate\Http\Response
     */
    public function campainNew()
    {
        try{

            $desired_status = 'ORIG';
            // $listCampain = CampainFX::where('status', $desired_status)
            //                 ->orderBy('created_at', 'desc') // Sort by creation date in descending order
            //                 ->get();
            $listCampain = DB::table('campainFX')
                        ->where('campainFX.status', $desired_status)
                        ->leftJoin('campainFX_Txn', 'campainFX.campainID', '=', 'campainFX_Txn.campainID')
                        ->select(
                            'campainFX.campainID',
                            'campainFX.campainName',
                            'campainFX.created_at',
                            'campainFX.campainDescription',
                            'campainFX.status',
                            'campainFX.content', // Include all columns used in SELECT
                            DB::raw('SUM(CASE WHEN campainFX_Txn.status = "Y" THEN campainFX_Txn.percent ELSE 0 END) as total_percent')
                        )
                        ->groupBy(
                            'campainFX.campainID',
                            'campainFX.campainName',
                            'campainFX.created_at',
                            'campainFX.campainDescription',
                            'campainFX.status',
                            'campainFX.content' // Include all columns used in SELECT
                        )
                        ->orderBy('campainFX.created_at', 'desc')
                        ->get();
   
            return view('campain.campain-new', compact(['listCampain']));
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors('Create campain fail.' + e);
        }
    }

    public function delete($id)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($id);
            $CampainFX_ID->delete();
            return redirect()->back()->with('success', 'Campain deleted successfully.');
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors('Delete campain fail.' + e);
        }
    }

    public function edit($id)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($id);
            return view('campain.campain-editor', compact(['CampainFX_ID']));
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors('Delete campain fail.' + e);
        }
    }

    public function detail($id)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id );
            $CampainFX_ID = CampainFX::findOrFail($id);
            // Group by campainID and sum the amount
            $sumsAmount = CampainFX_TXN::select('campainID', \DB::raw('SUM(amount) as total_amount'))
                                ->where('campainID', $CampainFX_ID->campainID)
                                ->where('status', 'Y')
                                ->groupBy('campainID')
                                ->get();
            return view('campain.campain-detail', compact(['CampainFX_ID', 'customer', 'sumsAmount']));
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors('Delete campain fail.' + e);
        }
    }

    public function run($id)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($id);
            $desired_status = $CampainFX_ID->status ?? '';
            if($CampainFX_ID->status == 'ORIG')
            {
                $CampainFX_ID->update(['status' => 'RUN']);
            }
            return redirect()->route('campain-history')
                ->withSuccess(' ' . $CampainFX_ID->campainName . ' run successfully.');
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors(' ' . $CampainFX_ID->campainName . ' run fail.');
        }
    }
    public function done($id)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($id);
            $desired_status = $CampainFX_ID->status ?? '';
            if($CampainFX_ID->status == 'RUN')
            {
                $CampainFX_ID->update(['status' => 'DONE']);
            }
            return redirect()->route('campain-history')
                ->withSuccess(' ' . $CampainFX_ID->campainName . ' done successfully.');
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors(' ' . $CampainFX_ID->campainName . ' done fail.');
        }
    }

    public function deposit(Request $request)
    {
        try{
            $user_id = Auth::user()->user_id;
            $customer = Customer::find($user_id );
            $CampainFX_ID = CampainFX::findOrFail($request->campain_id);
            $totalAmount = $CampainFX_ID->campain_amount ?? 0;
            $amountDeposit = $request->amount_deposit ?? 0;
            $percent = ($amountDeposit/$totalAmount) * 100;
            $roundedPercent = round($percent, 2); 
            CampainFX_Txn::recordTransaction($request, $customer->customer_id, $roundedPercent, 'DEPOSIT');

            return redirect()->route('campainFX.detail', $request->campain_id)
                ->withSuccess('Deposit to ' . $CampainFX_ID->campainName . ' with amount ' . $amountDeposit . ' USD successfully.');
        }
        catch (e){
            return redirect()->route('campain-new')
            ->withErrors('Deposit campain fail.' + e);
        }
    }


     /**
     * Display Campain List History.
     * @return \Illuminate\Http\Response
     */
    public function campainHistory()
    {
        try{

            $desired_status_RUN = 'RUN';
            $desired_status_DONE = 'DONE';
            $listCampainRUN = CampainFX::where('status', $desired_status_RUN)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            $listCampainDONE = CampainFX::where('status', $desired_status_DONE)
            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
            ->get();
            return view('campain.campain-history', compact(['listCampainRUN','listCampainDONE']));
        }
        catch (e){
            return redirect()->route('campain-history')
            ->withErrors('Create campain fail.' + e);
        }
    }

    /**
     * Display Campain Transaction List .
     * @return \Illuminate\Http\Response
     */
    public function transactionList()
    {
        try{

            $desired_status_NEW = 'ORIG';
            $desired_status_RUN = 'DONE';
            $listCampainNEW = CampainFX::where('status', $desired_status_NEW)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            $listCampainDONE = CampainFX::where('status', $desired_status_RUN)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            return view('campain.campain-transaction', compact(['listCampainNEW','listCampainDONE']));
        }
        catch (e){
            return redirect()->route('campain-transaction')
            ->withErrors('Create campain fail.' + e);
        }
    }

}