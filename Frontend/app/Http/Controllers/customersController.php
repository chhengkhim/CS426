<?php

namespace App\Http\Controllers;
use App\Models\customersModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\sellerModel;
use Illuminate\Support\Facades\Storage;



class customersController extends Controller
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
    $product = DB::table('product')
               ->where('product_status', 'Activate')
               ->orderBy('product_id', 'asc')
               ->get();

    $category = DB::table('category')
               ->orderBy('category_id', 'asc')
               ->get();

    $images = DB::table('product_images')
             ->orderBy('img_id', 'asc')
             ->get();

    return view('customer_Home', [
        'product' => $product,
        'category' => $category,
        'images' => $images
    ]);
}

    /**
    public function register(Request $register)
    {

        $customer = new customersModel();

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
    public function process_registers_customer(Request $register)
    {
        $validate = $register->validate([
        'customer_profile_images' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'full_name'        => 'required|string',
        'age'              => 'required|integer|min:1|max:120',
        'gender'           => 'required|string',
        'phone_number'     => 'required|string|max:15',
        'customers_email'  => 'required|email|unique:customers,customers_email',
        'password'         => 'required|string|min:8|confirmed',
    ]);

        if (sellerModel::where('seller_email', $register->customers_email)->exists()) {
    return back()->withErrors(['customers_email' => 'This email is already used by a seller.']);
}

        if ($register->hasFile('customer_profile_images')) {
            $path = $register->file('customer_profile_images')->storePublicly('Assets/profile', ['disk' => 'spaces']);
            $validate['customer_profile_images'] = Storage::disk('spaces')->url($path);
        }

        $validate['password'] = bcrypt($validate['password']);

        $customer = customersModel::create($validate);

        Auth::login($customer);

        return redirect('/Home');
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

    public function customer_profile()
    {
        $customer = DB::select("select * from customers where customer_id = ?", [Auth::id()]);

        return view('customerProfile', [
            'customer' => $customer,
        ]);
    }

    public function update_customerProfile()
    {
        $customer = DB::select("select * from customers where customer_id = ?", [Auth::id()]);

        return view('customerProfile_update', [
            'customer' => $customer,
        ]);
    }

    public function process_updateCustomerProfile(Request $request)
    {
        $rule = [
            'customer_profile_images' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'full_name' => 'required|string',
            'age' => 'required|integer|min:1|max:120',
            'customers_email' => 'required|email|unique:customers,customers_email,' . Auth::id() . ',customer_id',
            'phone_number' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ];


        $validate = $request->validate($rule);
        $customer = customersModel::find(Auth::id());

        if ($request->hasFile('customer_profile_images')) {
        if ($customer->customer_profile_images) {
            // Delete old file from spaces
            $oldPath = str_replace(Storage::disk('spaces')->url(''), '', $customer->customer_profile_images);
            Storage::disk('spaces')->delete(ltrim($oldPath, '/'));
        }

        $path = $request->file('customer_profile_images')->storePublicly('Assets/profile', ['disk' => 'spaces']);
        $validate['customer_profile_images'] = Storage::disk('spaces')->url($path);
    } else {
        $validate['customer_profile_images'] = $customer->customer_profile_images;
    }

        if ($request->filled('password')) {
        $validate['password'] = bcrypt($request->input('password'));
    } else {
        $validate['password'] = $customer->password;
    }

        $customer->update($validate);

        return redirect('/customer_profile')->with('success', 'Profile updated successfully.');
    }


public function delete_customerAccount(Request $request, $customer_id)
{
    $customer = customersModel::find($customer_id);

    if (!$customer) {
        return redirect('/customer_profile')->withErrors(['Customer not found.']);
    }

    // Check if any order items are neither received nor cancelled
    $pendingOrders = DB::table('orderitem')
        ->join('orders', 'orderitem.order_id', '=', 'orders.order_id')
        ->where('orders.customer_id', $customer_id)
        ->whereNotIn('orderitem.status', ['received', 'cancelled'])
        ->exists();

    if ($pendingOrders) {
        return redirect('/customer_profile')->withErrors([
            'Cannot delete account. You have pending orders that are neither received nor cancelled.'
        ]);
    }

    DB::transaction(function () use ($customer_id, $customer) {
        // 1. Delete all cart items for this customer
        DB::table('cartitem')
            ->where('customer_id', $customer_id)
            ->delete();

        // 2. Delete reviews by this customer
        DB::table('review')
            ->where('customer_id', $customer_id)
            ->delete();

        // 3. Update orders to preserve them (set customer_id to null)
        DB::table('orders')
            ->where('customer_id', $customer_id)
            ->update(['customer_id' => null]);

        // 4. Delete all messages associated with this customer
        DB::table('message')
            ->where('customer_id', $customer_id)
            ->delete();

        // 5. Delete the customer's profile image if it exists
        if ($customer->customer_profile_images) {
            $oldPath = str_replace(Storage::disk('spaces')->url(''), '', $customer->customer_profile_images);
            Storage::disk('spaces')->delete(ltrim($oldPath, '/'));
        }

        // 6. Finally delete the customer record
        $customer->delete();
    });

    Auth::guard('customer')->logout();
    return redirect('/login')->with('success', 'Your account has been deleted successfully.');
}



    public function customer_product_detail($product_id)
{
    $product = DB::select("select * from product where product_id = ?", [$product_id]);

    if (empty($product)) {
        // Use with() instead of withErrors() for single messages
        return redirect('/customer_Home')->with('error', 'Product not found.');
    }

    if($product[0]->product_status !== 'Activate') {
        return redirect('/customer_viewOrder')->with('error', 'This product has been DEACTIVATE due to some issue.');
    }

    $images = DB::select("select * from product_images where product_id = ?", [$product_id]);
    $category = DB::select("select * from category where category_id = ?", [$product[0]->category_id]);

    return view('customer_product_detail', [
        'product' => $product,
        'images' => $images,
        'category' => $category,
    ]);
}


    public function store_name(Request $request, $product_id) {
    $seller = DB::table('sellers')
        ->join('product', 'sellers.seller_id', '=', 'product.seller_id')
        ->where('product.product_id', $product_id)
        ->select('sellers.*')
        ->first();

    if(!$seller) {
        return back()->with('error', 'Seller not found for this product');
    }

    $product = DB::table('product')
        ->where('product_status', 'Activate')
        ->where('seller_id', $seller->seller_id)->get();
    $category = DB::table('category')->orderBy('category_id')->get();
    $images = DB::table('product_images')->orderBy('img_id')->get();

    return view('customer_viewStorepage', [
        'seller' => $seller,
        'product' => $product,
        'category' => $category,
        'images' => $images,
    ]);
}



    public function viewSpecificCategoryProduct(Request $request)
{
    $category_id = $request->input('category_id');

    // Get the selected category name
    $selectedCategory = DB::table('category')
                        ->where('category_id', $category_id)
                        ->first();

    // Get ACTIVE products for the selected category
    $product = DB::table('product')
                ->where('category_id', $category_id)
                ->where('product_status', 'Activate')
                ->orderBy('product_id', 'asc')
                ->get();

    // Get all categories for the dropdown
    $category = DB::table('category')
                ->orderBy('category_id', 'asc')
                ->get();

    // Get images for the products
    $productIds = $product->pluck('product_id')->toArray();
    $images = DB::table('product_images')
              ->whereIn('product_id', $productIds)
              ->orderBy('img_id', 'asc')
              ->get();

    return view('customer_viewSpecificProduct_category', [
        'products' => $product,
        'category' => $category,
        'images' => $images,
        'selectedCategory' => $selectedCategory
    ]);
}


    public function allcustomerMessages()
{
    $customerId = Auth::id();

    // Get all unique sellers the customer has messaged with
    $conversations = DB::table('message')
        ->join('sellers', 'message.seller_id', '=', 'sellers.seller_id')
        ->select(
            'sellers.seller_id',
            'sellers.full_name as seller_name',
            'sellers.seller_profile_img as seller_profile_img',
            DB::raw('MAX(message.created_at) as last_message_time'),
            DB::raw('(SELECT COUNT(*) FROM message WHERE seller_id = sellers.seller_id AND customer_id = '.$customerId.' AND is_read = false AND sender_type = \'seller\') as unread_count')
        )
        ->where('message.customer_id', $customerId)
        ->groupBy('sellers.seller_id', 'sellers.full_name', 'sellers.seller_profile_img')
        ->orderBy('last_message_time', 'desc')
        ->get();

    return view('allcustomer_messages', [
        'conversations' => $conversations,
    ])->with([
        'success' => session('success'),
        'error' => session('error')
    ]);
}

    public function customerMessageSeller($seller_id)
{
    $customerId = Auth::id();

    DB::table('message')
        ->where('customer_id', $customerId)
        ->where('seller_id', $seller_id)
        ->where('sender_type', 'seller')
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

    $messages = DB::table('message')
        ->where('customer_id', $customerId)
        ->where('seller_id', $seller_id)
        ->orderBy('message_id', 'asc')
        ->get();

    $seller = DB::table('sellers')
        ->where('seller_id', $seller_id)
        ->first();

    if (!$seller) {
        return redirect('/allcustomer_messages')->with('error', 'Seller not found.');
    }

    return view('customerMessageSeller', [
        'messages' => $messages,
        'seller' => $seller,
    ]);
}

    public function processSendMessageToSeller(Request $request, $seller_id)
{
    $validated = $request->validate([
        'message' => 'required|string|max:500',
    ]);

    $customerId = Auth::id();

    try {
        DB::table('message')->insert([
            'customer_id' => $customerId,
            'seller_id' => $seller_id,
            'messages' => $validated['message'],
            'is_read' => 0,
            'sender_type' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('customerMessageSeller', ['seller_id' => $seller_id])
               ->with('success', 'Message sent successfully.');

    } catch (\Exception $e) {
        return redirect()->route('customerMessageSeller', ['seller_id' => $seller_id])
               ->withInput()
               ->with('error', 'Failed to send message: ' . $e->getMessage());
    }
}

    public function customer_viewReviews($order_id)
{
    $customer_id = Auth::id();

    // Verify the order belongs to the authenticated customer
    $order = DB::table('orders')
        ->where('order_id', $order_id)
        ->where('customer_id', $customer_id)
        ->first();

    if (!$order) {
        return redirect()->route('customer_orders')->with('error', 'Order not found or access denied');
    }

    // Get order items with the customer's reviews for this specific order
    $orderItems = DB::table('orderitem')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->leftJoin('product_images', function($join) {
            $join->on('product.product_id', '=', 'product_images.product_id')
                 ->whereRaw('product_images.img_id = (
                     SELECT MIN(img_id)
                     FROM product_images
                     WHERE product_id = product.product_id
                 )');
        })
        ->leftJoin('review', function($join) use ($customer_id, $order_id) {
            $join->on('orderitem.product_id', '=', 'review.product_id')
                 ->where('review.customer_id', '=', $customer_id)
                 ->where('review.order_id', '=', $order_id);
        })
        ->where('orderitem.order_id', $order_id)
        ->select(
            'orderitem.*',
            'product.product_name',
            'product_images.img_url',
            'review.rating',
            'review.comment',
            'review.created_at as review_date'
        )
        ->get();

    return view('customer_viewReview', [
        'order' => $order,
        'orderItems' => $orderItems
    ]);
}
}
