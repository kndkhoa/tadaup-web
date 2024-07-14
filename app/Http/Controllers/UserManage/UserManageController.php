<?php

namespace App\Http\Controllers\UserManage;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;


class UserManageController extends Controller
{
    

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */

    

    public function showCustomerList()
    {
        $customers = Customer::all();
        return view('usermanage.customer-list', compact(['customers']));
    }

    public function showCustomerDetail(Request $request)
    {
        // try{
        //     // Validate the form data
        //     $request->validate([
        //         'current_password' => 'required',
        //         'new_password' => 'required|min:8|confirmed',
        //     ]);

        //     // Check if the current password is correct
        //     if (!Hash::check($request->current_password, Auth::user()->password)) {
        //         return back()->withErrors(['current_password' => 'Current password is incorrect']);
        //     }

        //     // Update the user's password
        //     Auth::user()->update(['password' => Hash::make($request->new_password)]);

        //     return back()->with('success', 'Password changed successfully!');
        // }
        // catch(e){
        //     return back()->withErrors(['fail' => 'Change password fail']);
        // }
        
    }

    
  

}