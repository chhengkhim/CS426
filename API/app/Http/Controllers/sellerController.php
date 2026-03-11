<?php

namespace App\Http\Controllers;

use App\Models\sellerModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\productimagesModel;
use App\Models\customersModel;

class sellerController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('Register');
    }

    public function Home()
    {
        return view('seller_Home');
    }

    /**
    public function register(Request $register)
    {

        $customer = new sellerModel();

        $customer->full_name = $register->full_name;
        $customer->age = $register->age;
        $customer->gender = $register->gender;
        $customer->customers_email = $register->email;
        $customer->password = bcrypt($register->password);
        $customer->save();

        return redirect('/Home')->with('success', 'Registration successful, please login.');
    }
     */
    //the function below is used to register a new customer similar to the commented one above
    public function process_Registers_seller(Request $register)
    {
        $validate = $register->validate([
            'seller_profile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'full_name'        => 'required|string',
            'seller_email'     => 'required|email|unique:sellers,seller_email',
            'password'         => 'required|string|min:8|confirmed',
            'store_name'       => 'required|string|max:255',
            'address_line'    => 'required|string',
            'city'            => 'required|string',
            'state'           => 'required|string',
            'zip'             => 'required|string',
            'phone_number'    => 'required|string|max:15',
        ]);

        // Check if email exists in customers table
        if (customersModel::where('customers_email', $register->seller_email)->exists()) {
            return back()->withErrors(['seller_email' => 'This email is already used by a customer.']);
        }

        // Handle file upload
        if ($register->hasFile('seller_profile_img')) {
            $path = $register->file('seller_profile_img')->store('Assets/profile', 'public');
            $validate['seller_profile_img'] = '/storage/' . $path;
        }

        // Create the concatenated address
        $validate['seller_address'] = "{$validate['address_line']}, {$validate['city']}, {$validate['state']}, {$validate['zip']}";

        // Remove address fields that aren't in the model
        unset($validate['address_line'], $validate['city'], $validate['state'], $validate['zip']);

        // Hash password
        $validate['password'] = bcrypt($validate['password']);

        try {
            $seller = sellerModel::create($validate);
            Auth::login($seller);
            return redirect('/seller_Home')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    /**
    public function check_login(Request $request)
    {
        // Validate input
        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email',
            'password'  => 'required|string',
        ]);

        // Attempt to find customer
        $customer = DB::table('customers')
            ->where('full_name', $request->full_name)
            ->where('customers_email', $request->email)
            ->first();

        if ($customer) {
            // Check if password matches
            if (Hash::check($request->password, $customer->password)) {
                // Success - you can store in session or redirect
                session(['customer_id' => $customer->customer_id]);

                return redirect('/Home')->with('success', 'Welcome to handcraft marketplace website, ID:'. $customer-> customer_id.' Name:'. $customer->full_name . '!');
            } else {
                return back()->withErrors(['password' => 'Incorrect password.']);
            }
        } else {
            return back()->withErrors(['email' => 'Please check your email or full name again.']);
        }
    }
     */


    public function logout(Request $request)
    {
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();
        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    public function sellerProfile()
    {
        $seller = Auth::user()->load(['products', 'messages']);
        return response()->json($seller);
    }

    public function update_sellerProfile(Request $request)
    {
        $seller = auth()->user();
        $validatedData = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'seller_address' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
        ]);
        $seller->update($validatedData);
        return response()->json([
            'message' => 'Profile updated successfully!',
            'seller' => $seller
        ]);
    }


    public function delete_sellerAccount()
    {
        $seller = auth()->user();
        if ($seller) {
            $seller->delete();
            return response()->json(null, 204);
        }
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }



    public function allSellerMessages()
    {
        $sellerId = auth()->id();
        $messages = \App\Models\messageModel::with('customer')->where('recipient_seller_id', $sellerId)->get();
        return response()->json([
            'message' => 'Messages retrieved successfully',
            'data' => $messages
        ]);
    }

    public function sellerMessageCustomer($customer_id)
    {
        $seller_id = Auth::id();

        DB::table('message')
            ->where('seller_id', $seller_id)
            ->where('customer_id', $customer_id)
            ->where('sender_type', 'customer')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messages = DB::table('message')
            ->where('customer_id', $customer_id)
            ->where('seller_id', $seller_id)
            ->orderBy('message_id', 'asc')
            ->get();

        $customer = DB::table('customers')
            ->where('customer_id', $customer_id)
            ->first();

        if (!$customer) {
            return redirect('/allSellerMassages')->with('error', 'Seller not found.');
        }

        return view('sellerMessageCustomer', [
            'messages' => $messages,
            'customer' => $customer,
        ]);
    }

    public function processSendMessageToCustomer(Request $request, $customer_id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        $seller_id = Auth::id();

        try {
            DB::table('message')->insert([
                'customer_id'         => $customer_id,
                'seller_id'           => $seller_id,
                'recipient_seller_id' => $seller_id,
                'subject'             => $validated['subject'],
                'messages'            => $validated['message'],
                'is_read'             => false,
                'sender_type'         => 'seller',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            return response()->json(['message' => 'Message sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    public function sellerMessageAdmin($admin_id)
    {
        $sellerId = Auth::id();

        // Mark admin's messages as read
        DB::table('message')
            ->where('admin_id', $admin_id)
            ->where('seller_id', $sellerId)
            ->where('sender_type', 'admin')
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messages = DB::table('message')
            ->where('admin_id', $admin_id)
            ->where('seller_id', $sellerId)
            ->orderBy('message_id', 'asc')
            ->get();

        $admin = DB::table('admin')
            ->where('admin_id', $admin_id)
            ->first();

        return view('sellerMessageAdmin', [
            'messages' => $messages,
            'admin' => $admin,
        ]);
    }

    public function processSendMessageToAdmin(Request $request, $admin_id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:500',
        ]);

        $sellerId = Auth::id();

        try {
            DB::table('message')->insert([
                'admin_id'            => $admin_id,
                'seller_id'           => $sellerId,
                'customer_id'         => 1,
                'recipient_seller_id' => $sellerId,
                'subject'             => $validated['subject'],
                'messages'            => $validated['message'],
                'is_read'             => false,
                'sender_type'         => 'seller',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            return response()->json(['message' => 'Message sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    // API: Get seller profile
    public function apiSellerProfile()
    {
        return response()->json(['message' => 'Seller profile API not implemented']);
    }

    // API: Update seller profile
    public function apiUpdateSellerProfile()
    {
        return response()->json(['message' => 'Update seller profile API not implemented']);
    }

    // API: Delete seller account
    public function apiDeleteSellerAccount()
    {
        return response()->json(['message' => 'Delete seller account API not implemented']);
    }

    // API: List seller messages
    public function apiAllSellerMessages()
    {
        return response()->json(['message' => 'All seller messages API not implemented']);
    }

    // API: Send message to customer
    public function apiProcessSendMessageToCustomer()
    {
        return response()->json(['message' => 'Send message to customer API not implemented']);
    }

    // API: Send message to admin
    public function apiProcessSendMessageToAdmin()
    {
        return response()->json(['message' => 'Send message to admin API not implemented']);
    }
}
