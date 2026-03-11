<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\customersModel;
use App\Models\sellerModel;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Try logging in as seller
        $seller = sellerModel::where('seller_email', $credentials['email'])
            ->where('full_name', $credentials['name'])
            ->first();

        if ($seller && Hash::check($credentials['password'], $seller->password)) {
            if ($seller->account_status === 'Deactivate') {
                return back()->withErrors(['login' => 'Your seller account has been deactivated.']);
            }
            Auth::guard('seller')->login($seller);
            $request->session()->regenerate();
            return redirect('/seller_Home');
        }

        // Try logging in as customer
        $customer = customersModel::where('customers_email', $credentials['email'])
            ->where('full_name', $credentials['name'])
            ->first();

        if ($customer && Hash::check($credentials['password'], $customer->password)) {
            if ($customer->account_status === 'Deactivate') {
                return back()->withErrors(['login' => 'Your customer account has been deactivated.']);
            }
            Auth::guard('customer')->login($customer);
            $request->session()->regenerate();
            return redirect('/customer_Home');
        }

        // Failed both
        return back()->withErrors(['login' => 'Invalid name, email, or password.']);
    }


public function logout(Request $request)
    {
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
