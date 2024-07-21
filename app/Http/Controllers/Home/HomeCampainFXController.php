<?php

// Add this line at the top of your BankController.php file


namespace App\Http\Controllers\Home;

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



class HomeCampainFXController extends Controller
{
    

    /**
     * Display Campain Editor.
     * @return \Illuminate\Http\Response
     */

    public function new()
    {
        try{
            $desired_status_ORIG = 'ORIG';
            $listCampain = CampainFX::where('status', $desired_status_ORIG)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            return view('home.campainList', compact(['listCampain']));
        }
        catch (e){
            return redirect()->route('home.campainList')
            ->withErrors('Campain fail.' + e);
        }
    }

    public function run()
    {
        try{
            $desired_status_RUN = 'RUN';
            $listCampain = CampainFX::where('status', $desired_status_RUN)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            //dd($bearerToken );
            // if ($response->successful()) {
            //     $banks = $response->json()['data'];
            // } else {
            //     throw new Exception('API call failed with status code: ' . $response->status());
            // }
           
            return view('home.campainList', compact(['listCampain']));
        }
        catch (e){
            return redirect()->route('home.campainList')
            ->withErrors('Campain fail.' + e);
        }
    }

    public function done()
    {
        try{
            $desired_status_DONE = 'DONE';
            $listCampain = CampainFX::where('status', $desired_status_DONE)
                            ->orderBy('updated_at', 'desc') // Sort by creation date in descending order
                            ->get();
            return view('home.campainList', compact(['listCampain']));
        }
        catch (e){
            return redirect()->route('home.campainList')
            ->withErrors('Campain fail.' + e);
        }
    }

    public function detail($id, Request $request)
    {
        try{
            $CampainFX_ID = CampainFX::findOrFail($id);
            $referral_id = $request->query('sponserid');
            if($referral_id){
                // Store the referral ID in session
                $request->session()->put('referral_id', $referral_id);
                $request->session()->put('referral_expiry', now()->addDays(30));
                // Store the referral ID in a cookie with a 30-day expiration
                $cookie = cookie('referral_id', $referral_id, 60 * 24 * 30); // 30 days
            }
            return view('home.campainDetail', compact(['CampainFX_ID']));
        }
        catch (e){
            return redirect()->route('home.campainDetail')
            ->withErrors('Campain fail.' + e);
        }
    }

    public function contact()
    {
        try{
            return view('home.contact');
        }
        catch (e){
            return redirect()->route('home.contact')
            ->withErrors('Campain fail.' + e);
        }
    }
}