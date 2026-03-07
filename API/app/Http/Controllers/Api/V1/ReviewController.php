<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\reviewModel;
use App\Models\orderItemModel;

class ReviewController extends Controller
{
    public function store(Request $request, $order_id)
    {
        \Log::info('ReviewController@store called with order_id: ' . $order_id);
        \Log::info('Request data: ' . json_encode($request->all()));

        $customer = auth()->user();
        \Log::info('Authenticated customer: ' . ($customer ? $customer->customer_id : 'null'));

        if (!$customer) {
            \Log::error('No authenticated customer found');
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string|max:1000',
                'product_id' => 'required|exists:product,product_id'
            ]);
            \Log::info('Validation passed: ' . json_encode($validated));

            $orderItem = orderItemModel::where('order_id', $order_id)
                ->where('product_id', $validated['product_id'])
                ->whereHas('order', function ($query) use ($customer) {
                    $query->where('customer_id', $customer->customer_id);
                })->first();

            \Log::info('Order item found: ' . ($orderItem ? 'yes' : 'no'));

            if (!$orderItem) {
                \Log::warning('Order item not found or unauthorized');
                return response()->json(['message' => 'Order item not found or you are not authorized to review it.'], 404);
            }

            $review = reviewModel::create([
                'customer_id' => $customer->customer_id,
                'product_id' => $validated['product_id'],
                'order_id' => $order_id,
                'rating' => $validated['rating'],
                'comment' => $validated['review'],
            ]);

            \Log::info('Review created successfully: ' . $review->review_id);

            $response = [
                'message' => 'Review submitted successfully!',
                'review' => $review
            ];
            
            \Log::info('Returning response: ' . json_encode($response));
            return response()->json($response, 201);

        } catch (\Exception $e) {
            \Log::error('Exception in ReviewController@store: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
}
