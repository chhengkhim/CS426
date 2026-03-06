<?php

use App\Http\Controllers\adminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hashpassword', function () {
    return bcrypt('12345678');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:admin');


Route::middleware('guest:admin')->controller(AuthController::class)->group(function () {
    Route::post('/process_check_login', 'check_login')->name('login');

    Route::get('/login', 'login')->name('login');
});

Route::middleware('auth:admin')->controller(adminController::class)->group(function(){
    Route::get('/Home','Home')->name('Home');

    Route::get('/viewCategoryProduct', 'viewCategoryProduct')->name('viewCategoryProduct');

    Route::get('/viewProductDetail/{product_id}', 'viewProductDetail')->name('viewProductDetail');

    Route::get('/storePage/{product_id}', 'storePage')->name('storePage');

    Route::get('/storePage_fromSellerManagement/{seller_id}', 'storePage_fromSellerManagement')
    ->name('storePage_fromSellerManagement');  

    Route::get('customerManagement', 'customerManagement')->name('customerManagement');

    Route::get('sellerManagement', 'sellerManagement')->name('sellerManagement');

    Route::get('reviewManagement', 'reviewManagement')->name('reviewManagement');

    Route::post('deactivateCustomer/{customer_id}', 'deactivateCustomer')->name('deactivateCustomer');

    Route::post('deactivateSellerStorePage/{seller_id}', 'deactivateSellerStorePage')->name('deactivateSellerStorePage');

    Route::post('deactivateSeller/{seller_id}', 'deactivateSeller')->name('deactivateSeller');

    Route::post('activateCustomer/{customer_id}', 'activateCustomer')->name('deactivateCustomer');

    Route::post('activateSeller/{seller_id}', 'activateSeller')->name('activateSeller');

    Route::post('activateSellerStorePage/{seller_id}', 'activateSellerStorePage')->name('activateSeller');

    Route::get('viewAllOrders', 'viewAllOrders')->name('viewAllOrders');

    Route::get('viewCustomerDetail/{customer_id}', 'viewCustomerDetails')->name('viewCustomerDetail');

    Route::post('/deactivateProduct/{product_id}', 'deactivateProduct')->name('deactivateProduct');

    Route::post('/activateProduct/{product_id}', 'activateProduct')->name('activateProduct');

    Route::get('/productManagement', 'productManagement')->name('productManagement');

    Route::post('/deactivateProduct_viewAllProduct/{product_id}', 'deactivateProduct_viewAllProduct')->name('deactivateProduct viewAllProduct');

    Route::post('/activateProduct_viewAllProduct/{product_id}', 'activateProduct_viewAllProduct')->name('activateProduct viewAllProduct');

    Route::get('/allAdminMessages', 'allAdminMessages')->name('allAdminMessages');

    Route::get('/adminMessageSeller/{seller_id}', 'adminMessageSeller')->name('adminMessageSeller');

    Route::post('/processAdminMessageToSeller/{seller_id}', 'processAdminMessageToSeller')->name('processAdminMessageToSeller');   

    Route::post('/deleteProduct/{product_id}', 'deleteProduct')->name('deleteProduct');
});