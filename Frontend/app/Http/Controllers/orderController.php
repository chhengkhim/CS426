<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ordersModel;
use App\Models\orderItemModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\paymentModel;

class orderController extends Controller
{
    public function orderNow_view(Request $request)
{
    $product_id = $request->product_id;
    $quantity = $request->quantity;

    // Validate quantity
    if ($quantity < 1) {
        return redirect('/customer_Home')->withErrors(['error' => 'Quantity must be at least 1.']);
    }

    $product = DB::select("select * from product where product_id = ?", [$product_id]);

    if (empty($product)) {
        return redirect('/customer_Home')->withErrors(['error' => 'Product not found.']);
    }

    // Check stock availability
    if ($quantity > $product[0]->stock_quantity) {
        return redirect('/customer_Home')->withErrors([
            'error' => "Only {$product[0]->stock_quantity} items available for {$product[0]->product_name}"
        ]);
    }

    $images = DB::select("select * from product_images where product_id = ?", [$product_id]);
    $category = DB::select("select * from category where category_id = ?", [$product[0]->category_id]);

    return view('orderNow', [
        'product' => $product,
        'images' => $images,
        'category' => $category,
        'quantity' => $quantity,
    ]);
}

    public function orderNow_process(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:product,product_id',
        'quantity' => 'required|integer|min:1',
        'phone_number' => 'required|string|max:15',
        'address_line' => 'required|string|max:100',
        'city' => 'required|string|max:50',
        'state' => 'required|string|max:50',
        'zip' => 'required|string|max:10',
        'payment_method' => 'required|in:credit_card,paypal',
    ]);

    $shipping_address = "{$request->address_line}, {$request->city}, {$request->state}, {$request->zip}";

    // Get the product with lock for update to prevent race conditions
    $product = DB::table('product')->where('product_id', $request->product_id)->lockForUpdate()->first();

    if (!$product) {
        return redirect('/customer_Home')->withErrors(['error' => 'Product not found.']);
    }

    // Check stock quantity
    if($product->stock_quantity < $request->quantity) {
        return redirect('/customer_Home')->withErrors(['error'=>'Sorry, the product quantity is not enough.']);
    }

    $totalAmount = $product->product_price * $request->quantity;
    $newStockQuantity = $product->stock_quantity - $request->quantity;

    // Start transaction
    DB::beginTransaction();
    try {
        // Create order
        $order = new ordersModel();
        $order->customer_id = Auth::id();
        $order->total_items = $request->quantity;
        $order->total_amount = $totalAmount;
        $order->shipping_address = $shipping_address;
        $order->phone_number = $request->phone_number;
        $order->save();

        // Create order item
        $orderItem = new orderItemModel();
        $orderItem->order_id = $order->order_id;
        $orderItem->product_id = $request->product_id;
        $orderItem->seller_id = $product->seller_id;
        $orderItem->quantity = $request->quantity;
        $orderItem->price_at_purchase = $product->product_price;
        $orderItem->save();

        // Create payment
        $payment = new paymentModel();
        $payment->order_id = $order->order_id;
        $payment->payment_method = $request->payment_method;
        $payment->payment_status = 'completed';
        $payment->save();

        // Update product stock - CORRECTED VERSION
        DB::table('product')
            ->where('product_id', $request->product_id)
            ->update(['stock_quantity' => $newStockQuantity]);

        // Commit transaction
        DB::commit();

        return redirect('/customer_Home')->with('success', 'Order placed successfully!');

    } catch (\Exception $e) {
        // Rollback transaction on error
        DB::rollBack();
        return redirect('/customer_Home')->withErrors(['error' => 'An error occurred while processing your order.']);
    }
}

    public function seller_viewOrder() {
    $sellerId = Auth::id();

    // Get all orders with their items for this seller's products
    $orders = DB::table('orders')
        ->join('orderitem', 'orders.order_id', '=', 'orderitem.order_id')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->where('product.seller_id', $sellerId)
        ->select('orders.*', 'orderitem.*', 'product.*')
        ->orderBy('orders.order_id', 'desc')
        ->get()
        ->groupBy('order_id'); // Group by order ID

    // Get all related data in single queries
    $customerIds = $orders->flatten()->pluck('customer_id')->unique()->toArray();
    $customers = DB::table('customers')->whereIn('customer_id', $customerIds)->get()->keyBy('customer_id');

    $productIds = $orders->flatten()->pluck('product_id')->unique()->toArray();
    $products = DB::table('product')->whereIn('product_id', $productIds)->get()->keyBy('product_id');
    $images = DB::table('product_images')->whereIn('product_id', $productIds)->get()->groupBy('product_id');

    $categoryIds = $products->pluck('category_id')->unique()->toArray();
    $categories = DB::table('category')->whereIn('category_id', $categoryIds)->get()->keyBy('category_id');

    return view('seller_viewOrder', [
        'orders' => $orders,
        'customers' => $customers,
        'products' => $products,
        'images' => $images,
        'categories' => $categories
    ]);
}

    public function updateOrderStatus(Request $request, $orderId)
{
    $validStatuses = ['pending', 'shipped', 'delivered', 'cancelled'];
    $productId = $request->product_id;

    // Get current order item status
    $currentStatus = DB::table('orderitem')
        ->where('order_id', $orderId)
        ->where('product_id', $productId)
        ->value('status');

    // Validate request
    if (!in_array($request->status, $validStatuses)) {
        return back()->with('error', 'Invalid status');
    }

    // Validate status transition
    if ($currentStatus === 'shipped' && $request->status === 'pending') {
        return back()->with('error', 'Cannot revert from shipped to pending');
    }
    if ($currentStatus === 'delivered' && in_array($request->status, ['pending', 'shipped'])) {
        return back()->with('error', 'Cannot revert from delivered');
    }
    if ($currentStatus === 'cancelled') {
        return back()->with('error', 'Cannot change status of cancelled order');
    }
    if ($request->status === 'cancelled' && $currentStatus !== 'pending') {
        return back()->with('error', 'Can only cancel pending orders');
    }

    // Update status in orderitem table
    DB::table('orderitem')
        ->where('order_id', $orderId)
        ->where('product_id', $productId)
        ->update(['status' => $request->status]);

    return back()->with('success', 'Status updated successfully');
}



 public function customer_viewOrder()
{
    $customerId = Auth::id();

    // Get all orders with their items
    $orders = DB::select("
        SELECT o.*, oi.*, p.*, c.*, pi.img_url, oi.status as item_status
        FROM orders o
        JOIN orderitem oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.product_id
        LEFT JOIN category c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC
    ", [$customerId]);

    // Get all order IDs for review check
    $orderIds = collect($orders)->pluck('order_id')->unique();

    // Check which orders have reviews
    $reviews = DB::table('review')
        ->where('customer_id', $customerId)
        ->whereIn('order_id', $orderIds)
        ->get()
        ->groupBy('order_id');

    return view('customer_viewOrder', [
        'orders' => $orders,
        'reviews' => $reviews
    ]);
}

    public function cancelOrReceivedOrder($order_id, Request $request)
{
    $customerId = Auth::guard('customer')->id();
    $product_id = $request->product_id;

    // Verify the order belongs to the customer and get the specific item
    $orderItem = DB::table('orderitem')
        ->join('orders', 'orderitem.order_id', '=', 'orders.order_id')
        ->where('orderitem.order_id', $order_id)
        ->where('orderitem.product_id', $product_id)
        ->where('orders.customer_id', $customerId)
        ->select('orderitem.*')
        ->first();

    if (!$orderItem) {
        return back()->with('error', 'Order item not found!');
    }

    // Handle cancellation for pending items
    if ($orderItem->status == 'pending') {
        DB::beginTransaction();
        try {
            // Update only this specific order item to cancelled
            DB::table('orderitem')
                ->where('order_id', $order_id)
                ->where('product_id', $product_id)
                ->update(['status' => 'cancelled']);

            // Restore stock for this item only
            DB::table('product')
                ->where('product_id', $product_id)
                ->increment('stock_quantity', $orderItem->quantity);

            DB::commit();
            return back()->with('success', 'Item cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
    // Handle marking as received for delivered items
    elseif ($orderItem->status == 'delivered') {
        DB::beginTransaction();
        try {
            // Update only this specific order item to received
            DB::table('orderitem')
                ->where('order_id', $order_id)
                ->where('product_id', $product_id)
                ->update(['status' => 'received']);

            DB::commit();
            return back()->with('success', 'Item marked as received!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
    else {
        return back()->with('error', "This item cannot be cancelled or marked as received in its current status.");
    }
}






public function showReviewForm($order_id, $product_id)
{
    $customer_id = Auth::id();

    // Verify the order item belongs to the customer and is received
    $orderItem = DB::table('orderitem')
        ->join('orders', 'orderitem.order_id', '=', 'orders.order_id')
        ->where('orderitem.order_id', $order_id)
        ->where('orderitem.product_id', $product_id)
        ->where('orders.customer_id', $customer_id)
        ->where('orderitem.status', 'received')
        ->select('orderitem.*')
        ->first();

    if (!$orderItem) {
        return redirect()->route('customer_viewOrder')->with('error', 'Order item not found or not eligible for review');
    }

    // Check if review already exists
    $existingReview = DB::table('review')
        ->where('customer_id', $customer_id)
        ->where('product_id', $product_id)
        ->where('order_id', $order_id) // Ensure review is linked to the same order
        ->first();

    if ($existingReview) {
        return back()->with('error', 'You have already reviewed this product');
    }

    // Get product details
    $product = DB::table('product')
        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
        ->where('product.product_id', $product_id)
        ->select(
            'product.product_id',
            'product.product_name',
            'product_images.img_url'
        )
        ->first();

    return view('review_order', [
        'order_id' => $order_id,
        'product' => $product
    ]);
}

public function submitReview(Request $request, $order_id)
{
    $customer_id = Auth::id();
    $product_id = $request->product_id;

    // Verify the order item belongs to the customer and is received
    $orderItem = DB::table('orderitem')
        ->join('orders', 'orderitem.order_id', '=', 'orders.order_id')
        ->where('orderitem.order_id', $order_id)
        ->where('orderitem.product_id', $product_id)
        ->where('orders.customer_id', $customer_id)
        ->where('orderitem.status', 'received')
        ->select('orderitem.*')
        ->first();

    if (!$orderItem) {
        return redirect()->route('customer_viewOrder')->with('error', 'Order item not found or not eligible for review');
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:product,product_id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000'
    ]);



    // Create new review
    DB::table('review')->insert([
        'customer_id' => $customer_id,
        'product_id' => $product_id,
        'order_id' => $order_id, // Store order_id for reference
        'rating' => $validated['rating'],
        'comment' => $validated['comment'],
        'created_at' => now()
    ]);

    return redirect('customer_viewOrder')->with('success', 'Thank you for your review!');
}


public function viewOrderReviews($order_id)
{
    $sellerId = Auth::id();

    // Verify the order contains seller's products and get customer_id
    $order = DB::table('orders')
        ->join('orderitem', 'orders.order_id', '=', 'orderitem.order_id')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->where('orders.order_id', $order_id)
        ->where('product.seller_id', $sellerId)
        ->select('orders.*')
        ->first();

    if (!$order) {
        return redirect()->route('seller_viewOrder')->with('error', 'Order not found');
    }

    // Get order items with reviews specific to this order
    $orderItems = DB::table('orderitem')
    ->join('product', 'orderitem.product_id', '=', 'product.product_id')
    ->leftJoin('review', function($join) use ($order_id) {
        $join->on('orderitem.product_id', '=', 'review.product_id')
             ->where('review.order_id', '=', $order_id);
    })
    ->leftJoin('customers', 'review.customer_id', '=', 'customers.customer_id')
    ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
    ->where('orderitem.order_id', $order_id)
    ->where('product.seller_id', $sellerId) // Add this line to filter by seller
    ->select(
        'orderitem.*',
        'product.product_name',
        'product_images.img_url',
        'review.rating',
        'review.comment',
        'review.created_at as review_date',
        'review.customer_id',
        DB::raw("CASE WHEN review.customer_id IS NULL THEN 'Former Customer' ELSE customers.full_name END as customer_name")
    )
    ->get();

    return view('seller_viewReview', [
        'order' => $order,
        'orderItems' => $orderItems
    ]);
}








    public function Process_addToCart(Request $request)
{
    // Get authenticated customer ID
    $customer_id = Auth::id();

    // Validate the request data
    $validated = $request->validate([
        'product_id' => 'required|exists:product,product_id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Get product stock information
    $product = DB::table('product')
               ->where('product_id', $validated['product_id'])
               ->select('product_name', 'stock_quantity')
               ->first();

    // Check if requested quantity exceeds available stock
    if ($validated['quantity'] > $product->stock_quantity) {
        return redirect()->back()
               ->withErrors(['error' => "{$product->product_name} only has {$product->stock_quantity} items available"]);
    }

    // Check if item already exists in cart
    $existingItem = DB::table('cartitem')
                    ->where('customer_id', $customer_id)
                    ->where('product_id', $validated['product_id'])
                    ->first();

    if ($existingItem) {
        // Calculate new total quantity (existing + new)
        $newQuantity = $existingItem->quantity + $validated['quantity'];

        // Verify the combined quantity doesn't exceed stock
        if ($newQuantity > $product->stock_quantity) {
            $available = $product->stock_quantity - $existingItem->quantity;
            $available = $available > 0 ? $available : 0;

            return redirect()->back()
                   ->withErrors(['error' => "You already have {$existingItem->quantity} in cart. Only {$available} more available for {$product->product_name}"]);
        }

        // Update quantity if item exists and stock is sufficient
        DB::table('cartitem')
          ->where('cart_item_id', $existingItem->cart_item_id)
          ->update([
              'quantity' => $newQuantity,
              'updated_at' => now()
          ]);
    } else {
        // Create new cart item if stock is sufficient
        DB::table('cartitem')->insert([
            'customer_id' => $customer_id,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return redirect()->back()
           ->with('status', 'Item added to cart successfully!');
}


    public function viewCart()
{
    $customer_id = Auth::id();

    // Get cart items with product details
    $cartItems = DB::table('cartitem')
        ->join('product', 'cartitem.product_id', '=', 'product.product_id')->where('product.product_status', 'Activate')
        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
        ->select(
            'cartitem.*',
            'product.product_name',
            'product.product_description',
            'product.product_price',
            'product.stock_quantity',
            'product_images.img_url'
        )
        ->where('cartitem.customer_id', $customer_id)
        ->get()
        ->groupBy('product_id'); // Group by product to handle multiple images

    // Calculate cart total
    $cartTotal = collect($cartItems)->sum(function ($items) {
        return $items->first()->product_price * $items->sum('quantity');
    });

    return view('customer_cart', [
        'cartItems' => $cartItems,
        'cartTotal' => $cartTotal
    ]);
}

public function process_updateQuantityCartItem(Request $request)
{
    $customer_id = Auth::id();

    $validated = $request->validate([
        'product_id' => 'required|exists:product,product_id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Get product stock
    $product = DB::table('product')
               ->where('product_id', $validated['product_id'])
               ->select('stock_quantity', 'product_name')
               ->first();

    // Check stock availability
    if ($validated['quantity'] > $product->stock_quantity) {
        return back()->withErrors([
            'error' => "Only {$product->stock_quantity} items available for {$product->product_name}"
        ]);
    }

    // Update cart item
    DB::table('cartitem')
      ->where('product_id', $validated['product_id'])
      ->where('customer_id', $customer_id)
      ->update([
          'quantity' => $validated['quantity'],
          'updated_at' => now()
      ]);

    return back()->with('status', 'Quantity updated successfully!');
}



    public function process_removeItemFromCart(Request $request)
{
    $customer_id = Auth::id();

    // Get the cart item ID safely
    $cart_item = DB::table('cartitem')
                ->where('product_id', $request->product_id)
                ->where('customer_id', $customer_id)
                ->first();

    if($cart_item){
        // Proper deletion
        DB::table('cartitem')
          ->where('cart_item_id', $cart_item->cart_item_id)
          ->delete();

        return back()->with('status', 'Item removed from cart.');
    }

    return back()->with('error', 'Item not found in your cart.');
}

    public function orderFromcart_view()
{
    $customer_id = Auth::id();

    // Get cart items with product details and stock info
    $cartItems = DB::table('cartitem')
        ->join('product', 'cartitem.product_id', '=', 'product.product_id')
        ->leftJoin('product_images', function($join) {
            $join->on('product.product_id', '=', 'product_images.product_id')
                 ->whereRaw('product_images.img_id IN (select MIN(img_id) from product_images group by product_id)');
        })
        ->select(
            'cartitem.cart_item_id',
            'cartitem.product_id',
            'cartitem.quantity as cart_quantity',
            'product.product_name',
            'product.product_description',
            'product.product_price',
            'product.stock_quantity',
            'product_images.img_url'
        )
        ->where('cartitem.customer_id', $customer_id)
        ->get();

    // Check stock availability and calculate totals
    $inStockItems = [];
    $outOfStockItems = [];
    $cartTotal = 0;

    foreach ($cartItems as $item) {
        if ($item->cart_quantity <= $item->stock_quantity) {
            // Item has sufficient stock
            $item->subtotal = $item->product_price * $item->cart_quantity;
            $cartTotal += $item->subtotal;
            $inStockItems[] = $item;
        } else {
            // Item doesn't have enough stock
            $item->available = $item->stock_quantity;
            $outOfStockItems[] = $item;
        }
    }

    return view('customer_orderFromCart', [
        'inStockItems' => $inStockItems,
        'outOfStockItems' => $outOfStockItems,
        'cartTotal' => $cartTotal,
        'canCheckout' => count($outOfStockItems) === 0 && count($inStockItems) > 0
    ]);
}


public function processCartCheckout(Request $request)
{
    $validated = $request->validate([
        'address_line' => 'required|string|max:100',
        'city' => 'required|string|max:50',
        'state' => 'required|string|max:50',
        'zip' => 'required|string|max:10',
        'payment_method' => 'required|in:credit_card,paypal',
        'phone_number' => 'required|string|max:15',
    ]);

    $customer_id = Auth::id();
    $shipping_address = "{$request->address_line}, {$request->city}, {$request->state}, {$request->zip}";

    // Get cart items with product details
    $cartItems = DB::table('cartitem')
        ->join('product', 'cartitem.product_id', '=', 'product.product_id')
        ->where('cartitem.customer_id', $customer_id)
        ->select(
            'cartitem.*',
            'product.product_name',
            'product.product_price',
            'product.stock_quantity',
            'product.seller_id'
        )
        ->get();

    // Verify stock before starting transaction
    foreach ($cartItems as $item) {
        if ($item->quantity > $item->stock_quantity) {
            return redirect()->route('cart.view')
                ->withErrors(['error' => "{$item->product_name} only has {$item->stock_quantity} items available"]);
        }
    }

    DB::beginTransaction();
    try {
        // Calculate totals
        $totalItems = $cartItems->sum('quantity');
        $totalAmount = $cartItems->sum(function ($item) {
            return $item->product_price * $item->quantity;
        });

        // Create order
        $order = new ordersModel();
        $order->customer_id = $customer_id;
        $order->total_items = $totalItems;
        $order->total_amount = $totalAmount;
        $order->shipping_address = $shipping_address;
        $order->phone_number = $request -> phone_number;
        $order->save();

        // Create order items and update stock
        foreach ($cartItems as $item) {
            // Create order item
            $orderItem = new orderItemModel();
            $orderItem->order_id = $order->order_id;
            $orderItem->product_id = $item->product_id;
            $orderItem->seller_id = $item->seller_id;
            $orderItem->quantity = $item->quantity;
            $orderItem->price_at_purchase = $item->product_price;
            $orderItem->save();

            // Update product stock
            DB::table('product')
                ->where('product_id', $item->product_id)
                ->update([
                    'stock_quantity' => DB::raw("stock_quantity - {$item->quantity}")
                ]);
        }

        // Create payment
        $payment = new paymentModel();
        $payment->order_id = $order->order_id;
        $payment->payment_method = $request->payment_method;
        $payment->payment_status = 'completed';
        $payment->save();

        // Clear the cart
        DB::table('cartitem')
            ->where('customer_id', $customer_id)
            ->delete();

        DB::commit();

        return redirect('/customer_Home')->with('success', 'Order placed successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('viewCart')
            ->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}

}
