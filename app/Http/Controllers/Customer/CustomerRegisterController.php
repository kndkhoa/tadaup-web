<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;


class CustomerRegisterController extends Controller
{
    

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user_id = Auth::user()->user_id;
        //$data = Customer::find($user_id)->getTreeData(1);
        //$data = $this->getDataRecursive($user_id); // Bắt đầu từ id gốc, đệ quy MLM
 
        // Lấy danh sách khách hàng cha và con theo cấu trúc cây
        $customers = Customer::getCustomerTreeForId($user_id);

        //Tree view
        $userTree = Customer::with('children')->find($user_id);
        if (!$userTree) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $tree = $this->buildTree($userTree);

        $customer = Customer::find($user_id ); // Xử lý dữ liệu customer
        $user = User::find($user_id ); // Xử lý dữ liệu user
        return view('customers.profile', compact(['customer','user', 'customers', 'tree']));
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

    public function edit()
    {
        $user_id = Auth::user()->user_id;
        $response = Http::get('https://api.vietqr.io/v2/banks');
        $banks = $response->json()['data'];
        $customer = Customer::find($user_id );
        return view('customers.update-profile', compact('customer','banks'));
    }



    public function update(Request $request, Customer $customer)
    {
        try{
            $user_id = Auth::user()->user_id;
            Customer::upsert([
                'user_id' => $user_id,
                'customer_id' => $user_id,
                'role_id' => 2,
                'full_name' => $request->fullname,
                'phone' => $request->phone,
                'address' => $request->address,
                'image_font_id' => $request->id_font,
                'image_back_id' => $request->id_back,
                'bank_account' => $request->bank_number,
                'bank_name' => $request->bankList,
                'ewalletAddress' => $request->ewallet_adress,
                'ewalletNetwork' => $request->ewallet_network,
                'user_sponser_id' => $request->userid_sponser
                ],
                ['user_id', 'customer_id'],
                ['role_id', 'full_name', 'phone', 'address', 'image_font_id', 'image_back_id','bank_account','bank_name', 'ewalletAddress', 'ewalletNetwork', 'user_sponser_id'  ]
            );
            return redirect()->route('profile')
            ->withSuccess('You have successfully update profile!');
        }
        catch(e){
            return back()->withErrors(['Update Info' => 'Update info fail']);
        }
    }

    public function showChangePasswordForm()
    {
        return view('customers.change-password');
    }

    public function changePassword(Request $request)
    {
        try{
            // Validate the form data
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Check if the current password is correct
            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }

            // Update the user's password
            Auth::user()->update(['password' => Hash::make($request->new_password)]);

            return back()->with('success', 'Password changed successfully!');
        }
        catch(e){
            return back()->withErrors(['fail' => 'Change password fail']);
        }
        
    }

    
  

}