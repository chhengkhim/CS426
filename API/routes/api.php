<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// === Public Routes ===
// These routes are accessible without a token.
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // The new unified registration route

// === Protected Routes ===
// These routes require a valid Sanctum token to be accessed.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Seller Routes ---
    Route::prefix('seller')->group(function () {
        Route::get('/profile', 'App\\Http\\Controllers\\sellerController@sellerProfile');
        Route::put('/profile', 'App\\Http\\Controllers\\sellerController@update_sellerProfile');
        // Route::delete('/', 'App\\Http\\Controllers\\sellerController@delete_sellerAccount');

        Route::get('/products', 'App\\Http\\Controllers\\productController@listSellerProducts');
        Route::post('/products', 'App\\Http\\Controllers\\productController@addProduct');
        Route::put('/products/{id}', 'App\\Http\\Controllers\\productController@updateProduct');
        Route::delete('/products/{id}', 'App\\Http\\Controllers\\productController@deleteProduct');

        Route::get('/orders', 'App\\Http\\Controllers\\orderController@seller_viewOrder');
        
        Route::get('/messages', 'App\\Http\\Controllers\\sellerController@allSellerMessages');
        Route::post('/messages/customer/{customer_id}', 'App\\Http\\Controllers\\sellerController@processSendMessageToCustomer');
        Route::post('/messages/admin/{admin_id}', 'App\\Http\\Controllers\\sellerController@processSendMessageToAdmin');
    });

    // --- Customer Routes ---
    Route::prefix('customer')->group(function () {
        Route::get('/profile', 'App\\Http\\Controllers\\customersController@customer_profile');
        // Route::put('/profile', 'App\\Http\\Controllers\\customersController@update_customerProfile');
        // Route::delete('/', 'App\\Http\\Controllers\\customersController@delete_customerAccount');

        Route::get('/products', 'App\\Http\\Controllers\\productController@listAllProducts');
        Route::get('/products/{id}', 'App\\Http\\Controllers\\productController@customer_product_detail');

        Route::get('/cart', 'App\\Http\\Controllers\\customersController@viewCart');
        Route::post('/cart', 'App\\Http\\Controllers\\customersController@Process_addToCart');
        Route::put('/cart/{item_id}', 'App\\Http\\Controllers\\customersController@process_updateQuantityCartItem');
        Route::delete('/cart/{item_id}', 'App\\Http\\Controllers\\customersController@process_removeItemFromCart');
        Route::post('/checkout', 'App\\Http\\Controllers\\orderController@placeOrderFromCart');

        Route::post('/orders', 'App\\Http\\Controllers\\orderController@orderNow_process');
        Route::get('/orders', 'App\\Http\\Controllers\\orderController@customer_viewOrder');
        
        Route::post('/messages/seller/{seller_id}', 'App\\Http\\Controllers\\customersController@processSendMessageToSeller');
    });

    // --- Shared Order & Review Routes ---
    Route::put('/orders/{id}/status', 'App\\Http\\Controllers\\orderController@updateOrderStatus');
    Route::get('/orders/{order_id}/reviews', 'App\\Http\\Controllers\\Api\\ReviewController@index');
    // Route::post('/orders/{order_id}/reviews', 'App\\Http\\Controllers\\Api\\ReviewController@store'); // Old route commented out
    Route::post('/v1/orders/{order_id}/reviews', 'App\\Http\\Controllers\\Api\\V1\\ReviewController@store'); // Old v1 route
});