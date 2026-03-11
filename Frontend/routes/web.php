<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\sellerController;
use App\Http\Controllers\productController;
use App\Http\Controllers\customersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\orderController;
use Illuminate\Support\Facades\Auth;


    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/login', [sellerController::class, 'login'])->name('login');

    Route::get('/register', [sellerController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:seller,customer');


Route::middleware('guest:seller,customer')->group(function () {
    Route::post('/process_check_login', [AuthController::class, 'login'])->name('process_login');

    Route::post('/process_Registers_seller', [sellerController::class, 'process_Registers_seller']);

    Route::post('/process_registers_customer', [customersController::class, 'process_registers_customer']);

});


Route::middleware('auth:seller')->controller(productController::class)->group(function(){
    Route::get('/seller_Home', 'Home')->name('Home');

    Route::get('/addProduct', 'addProduct')->name('addProduct');

    Route::post('/process_addProduct', 'process_addProduct');

    Route::get('/updateProduct/{product_id}', 'updateProduct');

    Route::post('/process_updateProduct', 'process_updateProduct')->name('process_updateProduct');

    Route::post('/deleteProduct/{product_id}', 'deleteProduct')->name('deleteProduct');
});


Route::middleware('auth:seller')->controller(sellerController::class)->group(function () {
    Route::get('sellerProfile', 'sellerProfile')->name('sellerProfile');

    Route::get('update_sellerProfile', 'update_sellerProfile')->name('update_sellerProfile');

    Route::post('process_edit_sellerProfile', 'process_edit_sellerProfile')->name('process_edit_sellerProfile');

    Route::post('/delete_sellerAccount/{seller_id}', 'delete_sellerAccount')->name('delete_sellerAccount');

    Route::get('/sellerMessageCustomer/{customer_id}', 'sellerMessageCustomer')->name('sellerMessageCustomer');

    Route::post('/processSendMessageToCustomer/{customer_id}', 'processSendMessageToCustomer')->name('processSendMessageToCustomer');

    Route::get('/allSellerMessages', 'allSellerMessages')->name('allSellerMessages');

    Route::get('/sellerMessageAdmin/{admin_id}', 'sellerMessageAdmin')->name('sellerMessageAdmin');

    Route::post('/processSendMessageToAdmin/{admin_id}', 'processSendMessageToAdmin')->name('processSendMessageToAdmin');
});



Route::middleware('auth:customer')->controller(customersController::class)->group(function () {
    Route::get('/customer_Home', 'Home')->name('Home');

    Route::get('/customer_profile', 'customer_profile')->name('customerProfile');

    Route::get('/customer_profile_update', 'update_customerProfile')->name('customer_profile_update');

    Route::post('/process_updateCustomerProfile', 'process_updateCustomerProfile')->name('process_updateCustomerProfile');

    Route::post('/delete_customerAccount/{customer_id}', 'delete_customerAccount')->name('delete_customerAccount');

    Route::get('/customer_product_detail/{product_id}', 'customer_product_detail')->name('customer_product_detail');

    Route::get('/customer_viewStorepage/{product_id}', 'store_name')->name('View seller page');

    Route::get('/customer_viewSpecificProduct_category', 'viewSpecificCategoryProduct')->name('viewSpecificCatagoryProduct0');

    Route::get('/customerMessageSeller/{seller_id}', 'customerMessageSeller')->name('customerMessageSeller');

    Route::post('/processSendMessageToSeller/{seller_id}', 'processSendMessageToSeller')->name('processSendMessageToSeller');

    Route::get('/allcustomer_messages', 'allcustomerMessages')->name('allcustomerMessages');

    Route::get('/customer_viewReviews/{order_id}', 'customer_viewReviews')->name('customer_viewReviews');
});


Route::middleware('auth:customer')->controller(orderController::class)->group(function () {
    Route::get('/orderNow/{product_id}', 'orderNow_view')->name('orderNow');

    Route::post('/orderNow_process', 'orderNow_process')->name('orderNow_process');

    Route::get('/customer_viewOrder', 'customer_viewOrder')->name('customer.orders');

    Route::post('/cancelOrReceivedOrder/{order_id}', 'cancelOrReceivedOrder')
     ->name('orders.cancel');

     Route::post('/Process_addToCart/{product_id}', 'Process_addToCart')->name('Process_addToCart');

    Route::get('/viewCart', 'viewCart')->name('viewCart');

    Route::post('/process_updateQuantityCartItem','process_updateQuantityCartItem')->name('process_updateQuantityCartItem');

    Route::post('/process_removeItemFromCart', 'process_removeItemFromCart')->name('process_removeItemFromCart');

    Route::get('/orderFromcart_view', 'orderFromcart_view')->name('orderFromcart_view');

    Route::post('/processCartCheckout', 'processCartCheckout')->name('processCartCheckout');

    // Review routes
    Route::get('showReviewForm/{order_id}/{product_id}', 'showReviewForm')->name('order.review');

    Route::post('submitReview/{order_id}', 'submitReview')->name('order.submit_review');

});

Route::middleware('auth:seller')->controller(orderController::class)->group(function () {
    Route::get('/seller_viewOrder', 'seller_viewOrder')->name('seller_viewOrder');

    Route::put('/orders/update-status/{order_id}', 'updateOrderStatus')
    ->name('orders.update-status');

    Route::get('/viewOrderReviews/{order_id}', [OrderController::class, 'viewOrderReviews'])
     ->name('order.reviews');
});
