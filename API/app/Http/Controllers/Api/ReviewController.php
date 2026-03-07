<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\reviewModel;
use App\Models\orderItemModel;

class ReviewController extends Controller
{
    public function store(Request $request, $order_id)
    {
        $customer = auth()->user();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
            'product_id' => 'required|exists:product,product_id'
        ]);
        $orderItem = orderItemModel::where('order_id', $order_id)
            ->where('product_id', $validated['product_id'])
            ->whereHas('order', function ($query) use ($customer) {
                $query->where('customer_id', $customer->customer_id);
            })->first();
        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found or you are not authorized to review it.'], 404);
        }
        $review = reviewModel::create([
            'customer_id' => $customer->customer_id,
            'product_id' => $validated['product_id'],
            'order_id' => $order_id,
            'rating' => $validated['rating'],
            'comment' => $validated['review'],
        ]);
        return response()->json([
            'message' => 'Review submitted successfully!',
            'review' => $review
        ], 201);
    }
    public function index($order_id)
    {
        $reviews = reviewModel::where('order_id', $order_id)->with('customer')->get();
        return response()->json($reviews);
    }
}
