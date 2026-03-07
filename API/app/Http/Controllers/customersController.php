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
    public function customer_profile()
    {
        $customer = Auth::user();
        return response()->json($customer);
    }

    public function update_customerProfile(Request $request)
    {
        $customer = Auth::user();
        if (!$customer) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $validatedData = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer',
            'gender' => 'sometimes|string',
            'phone_number' => 'sometimes|string|max:20',
        ]);
        $customer->update($validatedData);
        return response()->json([
            'message' => 'Profile updated successfully!',
            'customer' => $customer
        ]);
    }

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

        if ($register->hasFile('customer_profile_image')) {
    $path = $register->file('customer_profile_image')->store('Assets/profile', 'public');
    $validate['customer_profile_images'] = '/storage/' . $path;
}

        $validate['password'] = bcrypt($validate['password']);


        $customer = customersModel::create($validate);

        Auth::login($customer);


        return redirect('/login');
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


        if ($request->filled('password')) {
        $rules['password'] = 'required|string|min:8|confirmed';
    }

        $validate = $request->validate($rule);
        $customer = customersModel::find(Auth::id());

        if ($request->hasFile('customer_profile_images')) {
        if ($customer->customer_profile_images) {
            $oldPath = str_replace('/storage/', '', $customer->customer_profile_images);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('customer_profile_images')->store('Assets/profile', 'public');
        $validate['customer_profile_images'] = '/storage/' . $path;
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

        // 2. Update reviews to preserve them (set customer_id to null)
        DB::table('review')
            ->where('customer_id', $customer_id)
            ->update(['customer_id' => null]);

        // 3. Update orders to preserve them (set customer_id to null)
        DB::table('orders')
            ->where('customer_id', $customer_id)
            ->update(['customer_id' => null]);

        // 4. Delete the customer's profile image if it exists
        if ($customer->customer_profile_images) {
            $oldPath = str_replace('/storage/', '', $customer->customer_profile_images);
            Storage::disk('public')->delete($oldPath);
        }

        // 5. Finally delete the customer record
        $customer->delete();
    });

    Auth::guard('customer')->logout();
    return redirect('/login')->with('success', 'Your account has been deleted successfully.');
}



    public function customer_product_detail($product_id)
    {
        $product = \App\Models\productModel::with(['images', 'reviews', 'seller'])->find($product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
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
        $messages = \App\Models\messageModel::with(['seller', 'customer'])->where('customer_id', Auth::id())->get();
        return response()->json($messages);
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

    // API: Get customer profile
    public function apiCustomerProfile()
    {
        return response()->json(['message' => 'Customer profile API not implemented']);
    }

    // API: Update customer profile
    public function apiUpdateCustomerProfile()
    {
        return response()->json(['message' => 'Update customer profile API not implemented']);
    }

    // API: Delete customer account
    public function apiDeleteCustomerAccount()
    {
        return response()->json(['message' => 'Delete customer account API not implemented']);
    }

    // API: View cart
    public function apiViewCart()
    {
        return response()->json(['message' => 'View cart API not implemented']);
    }

    // API: Add to cart
    public function apiAddToCart()
    {
        return response()->json(['message' => 'Add to cart API not implemented']);
    }

    // API: Update cart item quantity
    public function apiUpdateCartItem()
    {
        return response()->json(['message' => 'Update cart item API not implemented']);
    }

    // API: Remove item from cart
    public function apiRemoveCartItem()
    {
        return response()->json(['message' => 'Remove cart item API not implemented']);
    }

    // API: Send message to seller
    public function apiSendMessageToSeller()
    {
        return response()->json(['message' => 'Send message to seller API not implemented']);
    }

    public function Process_addToCart(Request $request)
    {
        // Get the authenticated customer's ID
        $customerId = auth()->id();

        // Validate the incoming data
        $validated = $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product to check stock quantity
        $product = \App\Models\productModel::find($validated['product_id']);
        if ($validated['quantity'] > $product->stock_quantity) {
            return response()->json(['message' => 'Not enough stock available.'], 400);
        }

        // Check if the item is already in the cart
        $cartItem = \App\Models\cartitemModel::where('customer_id', $customerId)
                                            ->where('product_id', $validated['product_id'])
                                            ->first();

        if ($cartItem) {
            // If item exists, update the quantity
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // If item does not exist, create a new cart item
            $cartItem = \App\Models\cartitemModel::create([
                'customer_id' => $customerId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart successfully!',
            'cartItem' => $cartItem
        ], 201);
    }

    public function viewCart()
    {
        $cartItems = \App\Models\cartitemModel::with('product')->where('customer_id', Auth::id())->get();
        return response()->json($cartItems);
    }

    public function process_updateQuantityCartItem(Request $request, $itemId)
    {
        $customerId = auth()->id();

        // Validate the incoming data
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Find the cart item belonging to the authenticated customer
        $cartItem = \App\Models\cartitemModel::where('cart_item_id', $itemId)
                                            ->where('customer_id', $customerId)
                                            ->firstOrFail();

        // Check for stock
        $product = $cartItem->product;
        if ($validated['quantity'] > $product->stock_quantity) {
            return response()->json(['message' => 'Not enough stock available.'], 400);
        }

        // Update the quantity
        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'message' => 'Cart item updated successfully!',
            'cartItem' => $cartItem
        ]);
    }

    public function process_removeItemFromCart($itemId)
    {
        $customerId = auth()->id();

        // Find the cart item belonging to the authenticated customer
        $cartItem = \App\Models\cartitemModel::where('cart_item_id', $itemId)
                                            ->where('customer_id', $customerId)
                                            ->firstOrFail();

        // Delete the cart item
        $cartItem->delete();

        // Return a success response with no content, which is standard for a DELETE request
        return response()->json(null, 204);
    }
}
