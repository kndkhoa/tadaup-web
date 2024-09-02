<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CampainFX;
use App\Models\CampainFX_Txn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Check if referral ID is in session and not expired
        $sponserid = $request->session()->get('referral_id');
        $referralExpiry = $request->session()->get('referral_expiry');
        if ($sponserid && $referralExpiry && now()->greaterThan($referralExpiry)) {
            // Remove the referral ID if it has expired
            $request->session()->forget('referral_id');
            $request->session()->forget('referral_expiry');
            $sponserid = null;
        }
        // If referral ID is not in session, check the cookie
        else if (!$sponserid) {
            $sponserid = $request->cookie('referral_id');
            if(!$sponserid){
                $sponserid = $request->query('sponserid');
            }
        }
        return view('auth.register', compact('sponserid'));
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:250',
                'email' => 'required|email|max:250|unique:users',
                'password' => 'required|min:8|confirmed'
            ]);

            //check exists user_sponser_id
            if($request->sponser_userid){
                $customer = Customer::find($request->sponser_userid);
                if(!$customer)
                {
                    return redirect()->route('register')
                    ->withErrors(['error' => 'Sponser Userid not found.']);
                }
            }

            DB::transaction(function () use ($request) {
                $user = User::create([
                    'username' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'level' => 1,
                    'status' => 'ACT'
                ]);
                try {
                    Customer::create([
                        'customer_id' => $user->user_id,
                        'user_id' => $user->user_id,
                        'user_sponser_id' => $request->sponser_userid
                    ]);
                } 
                catch (QueryException $e) 
                {
                    Log::error('Error creating customer: ' . $e->getMessage());
                    throw new \Exception('Error creating customer: ' . $e->getMessage());
                }
           
            });
    
            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);
            $request->session()->regenerate();
            return redirect()->route('dashboard')
            ->withSuccess('You have successfully registered & logged in!');
        } catch (QueryException $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            return redirect()->route('register')
                ->withErrors(['error' => 'Create user failed. Please check the sponsor user ID and try again 1.']);
        } catch (Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return redirect()->route('register')
                ->withErrors(['error' => 'Create user failed. Please check the sponsor user ID and try again 2.']);
        }
        
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $email    = $request->input("email");
        $password = $request->input("password");
        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();
            return redirect()->route('dashboard')
                ->withSuccess('You have successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records.',
        ])->onlyInput('email');

    } 
    
    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try{
            if(Auth::check())
            {
                $user_id = Auth::user()->user_id;
                $customer = Customer::find($user_id );
                $desired_status = 'RUN';
                $listCampain = DB::table('campainFX')
                            ->where('campainFX.status', $desired_status)
                            ->leftJoin('campainFX_Txn', 'campainFX.campainID', '=', 'campainFX_Txn.campainID')
                            ->where('campainFX_Txn.customerID', $user_id)
                            ->select(
                                'campainFX.campainID',
                                'campainFX.campainName',
                                'campainFX.created_at',
                                'campainFX.campainDescription',
                                'campainFX.status',
                                'campainFX.content' // Include all columns used in SELECT
                            )
                            ->groupBy(
                                'campainFX.campainID',
                                'campainFX.campainName',
                                'campainFX.created_at',
                                'campainFX.campainDescription',
                                'campainFX.status',
                                'campainFX.content' 
                            )
                            ->orderBy('campainFX.created_at', 'desc')
                            ->get();
                return view('layouts.dashboard', compact(['listCampain']));
            }
            return redirect()->route('login')
                ->withErrors([
                'email' => 'Please login to access the dashboard.',
            ])->onlyInput('email');
        }
        catch (e){
            return redirect()->route('profile')
            ->withErrors('Create campain fail.' + e);
        }
    } 
    
    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }    

}