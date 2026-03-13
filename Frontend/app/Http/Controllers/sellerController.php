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
        return view('Login');
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
        $path = $register->file('seller_profile_img')->storePublicly('Assets/profile', ['disk' => 'spaces']);
        $validate['seller_profile_img'] = Storage::disk('spaces')->url($path);
    }

    // Create the concatenated address
    $validate['seller_address'] = "{$validate['address_line']}, {$validate['city']}, {$validate['state']}, {$validate['zip']}";

    // Remove address fields that aren't in the model
    unset($validate['address_line'], $validate['city'], $validate['state'], $validate['zip']);

    // Hash password
    $validate['password'] = bcrypt($validate['password']);

    try {
        $seller = sellerModel::create($validate);
        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
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




    public function sellerProfile()
    {
        $seller = DB::select("select * from sellers where seller_id = ?", [Auth::id()]);

        return view('sellerProfile', ['seller' => $seller]);
    }

    public function update_sellerProfile()
    {
        $seller = DB::select("select * from sellers where seller_id = ?", [Auth::id()]);

        return view('editSellerProfile', ['seller' => $seller]);
    }

    public function process_edit_sellerProfile(Request $request)
{
    $rules = [
        'seller_profile_img' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'full_name'          => 'required|string',
        'seller_email'       => 'required|email|unique:sellers,seller_email,' . Auth::id() . ',seller_id',
        'store_name'         => 'required|string|max:255',
        'phone_number'       => 'required|string|max:15',
    ];

    if ($request->filled('password')) {
        $rules['password'] = 'required|string|min:8|confirmed';
    }

    $validate = $request->validate($rules);

    $seller = sellerModel::find(Auth::id());

    // Handle profile image
    if ($request->hasFile('seller_profile_img')) {
        if ($seller->seller_profile_img) {
            // Delete old file from spaces
            $oldPath = str_replace(Storage::disk('spaces')->url(''), '', $seller->seller_profile_img);
            Storage::disk('spaces')->delete(ltrim($oldPath, '/'));
        }

        $path = $request->file('seller_profile_img')->storePublicly('Assets/profile', ['disk' => 'spaces']);
        $validate['seller_profile_img'] = Storage::disk('spaces')->url($path);
    } else {
        $validate['seller_profile_img'] = $seller->seller_profile_img;
    }

    // Handle password update only if provided
    if ($request->filled('password')) {
        $validate['password'] = bcrypt($request->input('password'));
    } else {
        $validate['password'] = $seller->password;
    }

    $seller->update($validate);

    return redirect('/sellerProfile')->with('success', 'Profile updated successfully!');
}


    public function delete_sellerAccount($seller_id) {
    $seller = sellerModel::find(Auth::id());

    if (!$seller) {
        return redirect('/sellerProfile')->with('error', 'Seller not found.');
    }

    // Check if any products have pending orders
    $pendingOrders = DB::table('orderitem')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->where('product.seller_id', $seller->seller_id)
        ->whereNotIn('orderitem.status', ['received', 'cancelled'])
        ->exists();

    if ($pendingOrders) {
        return redirect('/sellerProfile')->withErrors([
            'Cannot delete account. You have products with pending orders.'
        ]);
    }

    DB::transaction(function () use ($seller_id, $seller) {
        // 1. Delete all messages associated with this seller
        DB::table('message')
            ->where('seller_id', $seller->seller_id)
            ->delete();

        // 2. Get all products by seller
        $products = DB::table('product')->where('seller_id', $seller->seller_id)->get();

        foreach ($products as $product) {
            // Delete cart items for this product
            DB::table('cartitem')->where('product_id', $product->product_id)->delete();

            // Delete reviews for this product
            DB::table('review')->where('product_id', $product->product_id)->delete();

            // Delete completed/cancelled order items
            DB::table('orderitem')
                ->where('product_id', $product->product_id)
                ->whereIn('status', ['received', 'cancelled'])
                ->delete();

            // Delete product images
            $images = DB::table('product_images')->where('product_id', $product->product_id)->get();
            foreach ($images as $image) {
                $path = str_replace('storage/', '', $image->img_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                DB::table('product_images')->where('img_id', $image->img_id)->delete();
            }

            // Delete the product
            DB::table('product')->where('product_id', $product->product_id)->delete();
        }

        // Delete seller profile image
        if ($seller->seller_profile_img) {
            $oldPath = str_replace(Storage::disk('spaces')->url(''), '', $seller->seller_profile_img);
            Storage::disk('spaces')->delete(ltrim($oldPath, '/'));
        }

        // Finally delete the seller account
        $seller->delete();
    });

    Auth::logout();
    return redirect('/login')->with('success', 'Account deleted successfully.');
}



public function allSellerMessages()
{
    $seller_id = Auth::id();

    // Get customer conversations
    $customerConversations = DB::table('message')
        ->join('customers', 'message.customer_id', '=', 'customers.customer_id')
        ->select(
            'customers.customer_id',
            'customers.full_name as contact_name',
            'customers.customer_profile_images as profile_img',
            DB::raw("'customer' as contact_type"),
            DB::raw('MAX(message.created_at) as last_message_time'),
            DB::raw('(SELECT COUNT(*) FROM message WHERE customer_id = customers.customer_id AND seller_id = '.$seller_id.' AND is_read = false AND sender_type = \'customer\') as unread_count')
        )
        ->where('message.seller_id', $seller_id)
        ->whereNotNull('message.customer_id')
        ->groupBy('customers.customer_id', 'customers.full_name', 'customers.customer_profile_images');

    // Get admin conversations
    $allConversations = DB::table('message')
        ->join('admin', 'message.admin_id', '=', 'admin.admin_id')
        ->select(
            'admin.admin_id as contact_id',
            'admin.name as contact_name',
            DB::raw('NULL as profile_img'), // Since admin doesn't have profile image
            DB::raw("'admin' as contact_type"),
            DB::raw('MAX(message.created_at) as last_message_time'),
            DB::raw('(SELECT COUNT(*) FROM message WHERE admin_id = admin.admin_id AND seller_id = '.$seller_id.' AND is_read = false AND sender_type = \'admin\') as unread_count')
        )
        ->where('message.seller_id', $seller_id)
        ->whereNotNull('message.admin_id')
        ->groupBy('admin.admin_id', 'admin.name')
        ->union($customerConversations)
        ->orderBy('last_message_time', 'desc')
        ->get();

    return view('allSellerMessages', [
        'conversations' => $allConversations,
    ])->with([
        'success' => session('success'),
        'error' => session('error')
    ]);
}

    public function sellerMessageCustomer($customer_id)
{
    $seller_id = Auth::id();

    DB::table('message')
        ->where('seller_id', $seller_id)
        ->where('customer_id', $customer_id)
        ->where('sender_type', 'customer')
        ->where('is_read', false)
        ->update(['is_read' => true]);

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
        'message' => 'required|string|max:500',
    ]);

    $seller_id = Auth::id();

    try {
        DB::table('message')->insert([
            'customer_id' => $customer_id,
            'seller_id' => $seller_id,
            'messages' => $validated['message'],
            'is_read' => false,
            'sender_type' => 'seller',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('sellerMessageCustomer', ['customer_id' => $customer_id])
               ->with('success', 'Message sent successfully.');

    } catch (\Exception $e) {
        return redirect()->route('customerMessageSeller', ['cuustomer_id' => $customer_id])
               ->withInput()
               ->with('error', 'Failed to send message: ' . $e->getMessage());
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
        ->where('is_read', false)
        ->update(['is_read' => true]);

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
        'message' => 'required|string|max:500',
    ]);

    $sellerId = Auth::id();

    try {
        DB::table('message')->insert([
            'admin_id' => $admin_id,
            'seller_id' => $sellerId,
            'messages' => $validated['message'],
            'is_read' => false,
            'sender_type' => 'seller',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('sellerMessageAdmin', ['admin_id' => $admin_id])
               ->with('success', 'Message sent successfully.');

    } catch (\Exception $e) {
        return redirect()->route('sellerMessageAdmin', ['admin_id' => $admin_id])
               ->withInput()
               ->with('error', 'Failed to send message: ' . $e->getMessage());
    }
}
}

