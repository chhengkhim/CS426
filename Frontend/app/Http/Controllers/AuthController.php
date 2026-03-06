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
    if (Auth::guard('seller')->attempt([
        'full_name' => $credentials['name'],
        'seller_email' => $credentials['email'],
        'password' => $credentials['password']
    ])) {
        // Check if seller account is active
        $seller = Auth::guard('seller')->user();
        if ($seller->account_status === 'Deactivate') {
            Auth::guard('seller')->logout();
            return back()->withErrors(['login' => 'Your seller account has been deactivated.']);
        }

        $request->session()->regenerate();
        return redirect('/seller_Home');
    }

    // Try logging in as customer
    if (Auth::guard('customer')->attempt([
        'full_name' => $credentials['name'],
        'customers_email' => $credentials['email'],
        'password' => $credentials['password']
    ])) {
        // Check if customer account is active
        $customer = Auth::guard('customer')->user();
        if ($customer->account_status === 'Deactivate') {
            Auth::guard('customer')->logout();
            return back()->withErrors(['login' => 'Your customer account has been deactivated.']);
        }

        $request->session()->regenerate();
        return redirect('/customer_Home');
    }

    // Failed both
    return back()->withErrors(['login' => 'Invalid name or email or password.']);
}   


public function logout(Request $request)
    {
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
