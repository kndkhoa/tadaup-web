<?php

namespace App\Http\Controllers\UserManage;

use App\Models\Customer;
use App\Models\User;
use App\Models\CustomerConnection;
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

    public function showCustomerDetail($cutomerID)
    {
        try{
            $customerByID = Customer::find($cutomerID );
            $Customer_connection = Customer::where('customers.customer_id', $cutomerID)
                            ->join('cutomer_connection', 'customers.customer_id', '=', 'cutomer_connection.customer_id')
                            ->where('cutomer_connection.status', 'ACTIVE')
                            ->select('cutomer_connection.*', 'customers.full_name as customer_name', 'customers.phone as phone') 
                            ->get();
            $data = compact('Customer_connection', 'customerByID');
            return view('usermanage.customer-detail', $data);
        }
        catch (e){
            return redirect()->route('showCustomerList')
            ->withErrors('Get All Campaign Fail.' + e);
        }
        
    }

    public function creatConnection(Request $request)
    {
        try{
            CustomerConnection::addRecord($request);

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