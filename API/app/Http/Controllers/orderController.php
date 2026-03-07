<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ordersModel;
use App\Models\reviewModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class orderController extends Controller
{
    public function seller_viewOrder()
    {
        $sellerId = auth()->id();
        $orders = ordersModel::whereHas('items.product', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId);
        })->with(['items.product.images', 'customer'])->get();
        return response()->json(['message' => 'Orders retrieved successfully', 'data' => $orders]);
    }

    public function customer_viewOrder()
    {
        $customerId = auth()->id();
        $orders = ordersModel::where('customer_id', $customerId)
                                         ->with('items.product')
                                         ->get();
        return response()->json(['message' => 'Orders retrieved successfully', 'data' => $orders]);
    }

    public function updateOrderStatus(Request $request, $order_id)
    {
        $user = auth()->user();
        $order = ordersModel::findOrFail($order_id);
        $isSellerOfProduct = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('seller_id', $user->seller_id);
        })->exists();
        if (!$isSellerOfProduct) {
            return response()->json(['message' => 'You are not authorized to update this order.'], 403);
        }
        $validated = $request->validate(['status' => 'required|string|in:pending,shipped,delivered,cancelled']);
        $order->items()->update(['status' => $validated['status']]);
        $order->load('items');
        return response()->json(['message' => 'Order status updated successfully!', 'order' => $order]);
    }

    public function placeOrderFromCart(Request $request)
    {
        $customerId = auth()->id();
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);
        $cartItems = \App\Models\cartitemModel::where('customer_id', $customerId)->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }
        return DB::transaction(function () use ($cartItems, $customerId, $validated) {
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($item->quantity > $product->stock_quantity) {
                    throw new \Exception('Not enough stock for product: ' . $product->product_name);
                }
                $totalAmount += $item->quantity * $product->product_price;
            }
            $order = ordersModel::create([
                'customer_id' => $customerId,
                'total_items' => $cartItems->sum('quantity'),
                'total_amount' => $totalAmount,
                'shipping_address' => $validated['shipping_address'],
                'phone_number' => $validated['phone_number'],
            ]);
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->seller_id,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->product_price,
                ]);
                $item->product->decrement('stock_quantity', $item->quantity);
            }
            \App\Models\cartitemModel::where('customer_id', $customerId)->delete();
            return response()->json(['message' => 'Order placed successfully!', 'order' => $order->load('items')], 201);
        });
    }

    public function orderNow_view($product_id)
    {
        // Implementation for direct order placement
        return response()->json(['message' => 'Direct order placement not implemented yet.'], 501);
    }

    public function orderNow_process(Request $request)
    {
        // Implementation for direct order placement
        return response()->json(['message' => 'Direct order placement not implemented yet.'], 501);
    }

    public function Process_addToCart($product_id)
    {
        // Implementation for adding to cart
        return response()->json(['message' => 'Add to cart not implemented yet.'], 501);
    }

    public function viewCart()
    {
        // Implementation for viewing cart
        return response()->json(['message' => 'View cart not implemented yet.'], 501);
    }

    public function process_updateQuantityCartItem(Request $request)
    {
        // Implementation for updating cart item quantity
        return response()->json(['message' => 'Update cart quantity not implemented yet.'], 501);
    }

    public function process_removeItemFromCart(Request $request)
    {
        // Implementation for removing item from cart
        return response()->json(['message' => 'Remove from cart not implemented yet.'], 501);
    }

    public function orderFromcart_view()
    {
        // Implementation for cart checkout view
        return response()->json(['message' => 'Cart checkout view not implemented yet.'], 501);
    }

    public function processCartCheckout(Request $request)
    {
        // Implementation for cart checkout process
        return response()->json(['message' => 'Cart checkout not implemented yet.'], 501);
    }

    public function showReviewForm($order_id, $product_id)
    {
        // Implementation for showing review form
        return response()->json(['message' => 'Review form not implemented yet.'], 501);
    }

    public function cancelOrReceivedOrder($order_id)
    {
        // Implementation for canceling or receiving order
        return response()->json(['message' => 'Cancel/receive order not implemented yet.'], 501);
    }
}
