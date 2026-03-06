<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(){
        return view('Login');
    }

    public function check_login(Request $request)
{
    $credentials = $request->validate([
        'name'=>'required|string',
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if(Auth::guard('admin')->attempt(['name' => $credentials['name'], 'email'=> $credentials['email'], 'password'=>$credentials['password']])){
        $request->session()->regenerate();
        return redirect('/Home');
    }


    // Failed both
    return back()->withErrors(['login' => 'Invalid name or email or password.']);
}

public function logout(Request $request)
    {
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();
        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
