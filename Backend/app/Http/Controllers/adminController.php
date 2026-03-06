<?php

namespace App\Http\Controllers;

use App\Models\customersModel;
use App\Models\productimagesModel;
use App\Models\productModel;
use App\Models\sellerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class adminController extends Controller
{
    public function Home() {
    $product = DB::table('product')->orderBy('product_id')->get();
    $category = DB::table('category')->orderBy('category_id')->get();
    $images = DB::table('product_images')->orderBy('img_id')->get();
    
    // Get total count
    $totalProducts = DB::table('product')->count();
    $totalCategory = DB::table('category')->count();
    $totalSeller = DB::table('sellers')->count();
    $totalCustomer = DB::table('customers')->count();
    $totalOrder = DB::table('orders')->count();


    return view('Home', [
        'product' => $product,
        'category' => $category,
        'images' => $images,
        'totalProducts' => $totalProducts,
        'totalCategory' => $totalCategory,
        'totalSeller' => $totalSeller,
        'totalCustomer' => $totalCustomer,
        'totalOrder' => $totalOrder,
    ]);
}

    public function viewCategoryProduct(Request $request){
    $category_id = $request->input('category_id');
    
    // Get the selected category name
    $selectedCategory = DB::table('category')
                        ->where('category_id', $category_id)
                        ->first();

    // Get products for the selected category
    $product = DB::table('product')
                ->where('category_id', $category_id)
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

    return view('viewCategoryProduct', [
        'products' => $product,
        'category' => $category,
        'images' => $images,
        'selectedCategory' => $selectedCategory
    ]);
    }



    public function storePage($product_id) {
    // Get the seller based on the given product ID
    $seller = DB::table('sellers')
        ->join('product', 'sellers.seller_id', '=', 'product.seller_id')
        ->where('product.product_id', $product_id)
        ->select('sellers.*')
        ->first();

    if (!$seller) {
        return back()->with('error', 'Seller not found for this product');
    }

    // Get all products by this seller
    $product = DB::table('product')
        ->where('seller_id', $seller->seller_id)
        ->get();

    // Get all categories
    $category = DB::table('category')
        ->orderBy('category_id')
        ->get();

    // Get all images
    $images = DB::table('product_images')
        ->orderBy('img_id')
        ->get();

    // Calculate total earnings of the seller from all their product sales
    $totalEarnings = DB::table('orderitem')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->where('product.seller_id', $seller->seller_id)
        ->sum('orderitem.price_at_purchase');

    return view('storePage', [
        'seller' => $seller,
        'product' => $product,
        'category' => $category,
        'images' => $images,
        'totalEarnings' => $totalEarnings, // pass it to the view
    ]);
}


public function storePage_fromSellerManagement($seller_id) {
    $seller = DB::table('sellers')
        ->where('seller_id', $seller_id)
        ->select('sellers.*')
        ->first();

    if (!$seller) {
        return back()->with('error', 'Seller not found for this product');
    }

    // Get all products by this seller
    $product = DB::table('product')
        ->where('seller_id', $seller->seller_id)
        ->get();

    // Get all categories
    $category = DB::table('category')
        ->orderBy('category_id')
        ->get();

    // Get all images
    $images = DB::table('product_images')
        ->orderBy('img_id')
        ->get();

    // Calculate total earnings of the seller from all their product sales
    $totalEarnings = DB::table('orderitem')
        ->join('product', 'orderitem.product_id', '=', 'product.product_id')
        ->where('product.seller_id', $seller->seller_id)
        ->sum('orderitem.price_at_purchase');

    return view('storePage', [
        'seller' => $seller,
        'product' => $product,
        'category' => $category,
        'images' => $images,
        'totalEarnings' => $totalEarnings, // pass it to the view
    ]);
}

    public function customerManagement(){
        $customer = DB::table('customers')->get();

        return view('customerManagement',[
            'customer' =>$customer,
        ]);
    }

    public function sellerManagement(){
        $seller = DB::table('sellers')->get();

        return view('sellerManagement',[
            'seller' =>$seller,
        ]);
    }

public function reviewManagement() {
    // Get all reviews with related order, product, customer, and seller info
    $reviews = DB::table('review')
        ->join('orders', 'review.order_id', '=', 'orders.order_id')
        ->join('orderitem', function($join) {
            $join->on('orders.order_id', '=', 'orderitem.order_id')
                 ->on('review.product_id', '=', 'orderitem.product_id');
        })
        ->join('product', 'review.product_id', '=', 'product.product_id')
        ->leftJoin('customers', 'review.customer_id', '=', 'customers.customer_id') // Changed to leftJoin
        ->join('sellers', 'product.seller_id', '=', 'sellers.seller_id')
        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
        ->select(
            'orders.order_id',
            'orders.created_at as order_date',
            'review.review_id',
            'review.rating',
            'review.comment as review_message',
            'review.created_at as review_date',
            'review.customer_id',
            'product.product_id',
            'product.product_name',
            'product_images.img_url as product_image',
            DB::raw('IF(review.customer_id IS NULL, "Former Customer", customers.full_name) as customer_name'),
            DB::raw('IF(review.customer_id IS NULL, NULL, customers.customer_profile_images) as customer_image'),
            'sellers.seller_id',
            'sellers.full_name as seller_name',
            'sellers.store_name'
        )
        ->orderBy('review.created_at', 'desc')
        ->get();

    return view('reviewManagement', [
        'reviews' => $reviews
    ]);
}

public function deactivateCustomer($customer_id)
{
    DB::transaction(function () use ($customer_id) {
        $customer = customersModel::findOrFail($customer_id);
        if (!$customer) {
        return redirect('/customerManagement')->with('error', 'Customer not found.');
    }
    
    
        $customer->account_status = 'Deactivate';

        // 5. Delete customer record
        $customer->update();
    });

    return redirect()->back()->with('success', 'Customer account is deactivate.');
}

public function activateCustomer($customer_id)
{
    DB::transaction(function () use ($customer_id) {
        $customer = customersModel::findOrFail($customer_id);
        if (!$customer) {
        return redirect('/customerManagement')->with('error', 'Customer not found.');
    }
    
    
        $customer->account_status = 'Activate';

        // 5. Delete customer record
        $customer->update();
    });

    return redirect()->back()->with('success', 'Customer account is activate.');
}



public function deactivateSeller($seller_id)
{
    DB::transaction(function () use ($seller_id) {
        $seller = sellerModel::find($seller_id);
        
        if (!$seller) {
            return redirect('/sellerManagement')->with('error', 'Seller not found.');
        }
        
        // 1. Deactivate the seller
        $seller->account_status = 'Deactivate';
        $seller->save();
        
        // 2. Deactivate all products belonging to this seller
        DB::table('product')
            ->where('seller_id', $seller_id)
            ->update(['product_status' => 'Deactivate']);
        
        });
        return redirect('/sellerManagement')->with('success', 'Seller and all associated products have been deactivated.');
}


public function activateSeller($seller_id)
{
    DB::transaction(function () use ($seller_id) {
    $seller = sellerModel::find($seller_id);
    
    if (!$seller) {
        return redirect('/sellerManagement')->with('error', 'Seller not found.');
    }
    
        // 1. Deactivate the seller
        $seller->account_status = 'Activate';
        $seller->save();
        
        // 2. Deactivate all products belonging to this seller
        DB::table('product')
            ->where('seller_id', $seller_id)
            ->update(['product_status' => 'Activate']);

        });
        return redirect('/sellerManagement')->with('success', 'Account seller activate successfully.');
}

public function deactivateSellerStorePage($seller_id)
{
    DB::transaction(function () use ($seller_id) {
        $seller = sellerModel::find($seller_id);
        
        if (!$seller) {
            return redirect('/sellerManagement')->with('error', 'Seller not found.');
        }
        
        // 1. Deactivate the seller
        $seller->account_status = 'Deactivate';
        $seller->save();
        
        // 2. Deactivate all products belonging to this seller
        DB::table('product')
            ->where('seller_id', $seller_id)
            ->update(['product_status' => 'Deactivate']);
        
        });
        return redirect()-> route('storePage_fromSellerManagement', ['seller_id' => $seller_id])->with('success', 'Seller and all associated products have been deactivated.');
}


public function activateSellerStorePage($seller_id)
{
    DB::transaction(function () use ($seller_id) {
    $seller = sellerModel::find($seller_id);
    
    if (!$seller) {
        return redirect('/storePage_fromSellerManagement')->with('error', 'Seller not found.');
    }
    
        // 1. Deactivate the seller
        $seller->account_status = 'Activate';
        $seller->save();
        
        // 2. Deactivate all products belonging to this seller
        DB::table('product')
            ->where('seller_id', $seller_id)
            ->update(['product_status' => 'Activate']);

        });
        return redirect()-> route('storePage_fromSellerManagement', ['seller_id' => $seller_id])->with('success', 'Seller and all associated products have been deactivated.');
}



public function deleteProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        
        if (!$product) {
            return redirect('/productManagement')->withErrors('Product not found.');
        }

        // Check if product has any pending orders
        $pendingOrders = DB::table('orderitem')
            ->where('product_id', $product_id)
            ->whereNotIn('status', ['received', 'cancelled'])
            ->exists();

        if ($pendingOrders) {
            return redirect()->back()->withErrors('Cannot delete product. There are pending orders for this product. If you want to delete this product, you can deactivate it and wait for the prodict to be received or cancelled then you can delete it.');
        }

        // 1. Delete from cart items first (no constraints)
        DB::table('cartitem')->where('product_id', $product_id)->delete();

        // 2. Delete reviews (has foreign key to product)
        DB::table('review')->where('product_id', $product_id)->delete();

        // 3. Delete order items (only if status is received/cancelled)
        DB::table('orderitem')
            ->where('product_id', $product_id)
            ->whereIn('status', ['received', 'cancelled'])
            ->delete();

        // 4. Delete product images
        $images = productimagesModel::where('product_id', $product_id)->get();
        foreach ($images as $image) {
            $path = str_replace('/storage/', '', $image->img_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $image->delete();
        }

        // 5. Finally delete the product
        $product->delete();
    });

    return redirect()->back()->with('success', 'Product deleted successfully!');
}

public function deactivateProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        if (!$product) {
            throw new \Exception('Product not found.');
        }
        DB::table('message')->insert([
        'admin_id' => Auth::id(),
        'seller_id' => $product->seller_id,
        'messages' => "Your product '{$product->product_name}' has been deactivated. Please contact admin for more information.",
        'is_read' => 0,
        'sender_type' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

        DB::table('product')
            ->where('product_id', $product_id)
            ->update(['product_status' => 'Deactivate']); // Consider using consistent status values
    });

    return redirect()->route('storePage', ['product_id' => $product_id])
                    ->with('success', 'Product Deactivated successfully!');

}

public function activateProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        if (!$product) {
            throw new \Exception('Product not found.');
        }

        DB::table('message')->insert([
        'admin_id' => Auth::id(),
        'seller_id' => $product->seller_id,
        'messages' => "Your product '{$product->product_name}' has been aactivated. Thank you for your patience.",
        'is_read' => 0,
        'sender_type' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

        DB::table('product')
            ->where('product_id', $product_id)
            ->update(['product_status' => 'Activate']); // Consider using consistent status values
    });

    

    return redirect()->route('storePage', ['product_id' => $product_id])
                    ->with('success', 'Product Activated successfully!');

}




    public function viewProductDetail($product_id){

    $seller = DB::table('sellers')
        ->join('product', 'sellers.seller_id', '=', 'product.seller_id')
        ->where('product.product_id', $product_id)
        ->select('sellers.store_name')
        ->first();

    $product = DB::select("select * from product where product_id = ?", [$product_id]);

    if (empty($product)) {
        // Use with() instead of withErrors() for single messages
        return redirect('/customer_Home')->with('error', 'Product not found.');
    }

    $images = DB::select("select * from product_images where product_id = ?", [$product_id]);
    $category = DB::select("select * from category where category_id = ?", [$product[0]->category_id]);

    return view('viewProductDetail', [
        'seller' => $seller,
        'product' => $product,
        'images' => $images,
        'category' => $category,
    ]);
}

public function viewAllOrders()
{
    // Get orders with customer info
    $orders = DB::table('orders')
        ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
        ->select('orders.*', 'customers.full_name as customer_name')
        ->orderBy('orders.created_at', 'desc')
        ->paginate(10);

    // Get all related data in one query
    $orderDetails = [];
    if ($orders->count()) {
        $orderIds = $orders->pluck('order_id');
        
        $orderDetails = DB::table('orderitem')
            ->join('product', 'orderitem.product_id', '=', 'product.product_id')
            ->join('sellers', 'product.seller_id', '=', 'sellers.seller_id')
            ->leftJoin('product_images', function($join) {
                $join->on('product.product_id', '=', 'product_images.product_id')
                     ->whereRaw('product_images.img_id = (SELECT MIN(img_id) FROM product_images WHERE product_images.product_id = product.product_id)');
            })
            ->whereIn('orderitem.order_id', $orderIds)
            ->select(
                'orderitem.*',
                'product.product_name',
                'product.product_id',
                'sellers.store_name as seller_name',
                'product_images.img_url as product_image',
                'orderitem.status as item_status' // Added status field
            )
            ->get()
            ->groupBy('order_id');
    }

    return view('orderManagement', [
        'orders' => $orders,
        'orderDetails' => $orderDetails
    ]);
}

public function viewCustomerDetails($customer_id)
{
    // Get customer basic info
    $customer = DB::table('customers')
        ->where('customer_id', $customer_id)
        ->first();

    if (!$customer) {
        return redirect()->back()->with('error', 'Customer not found');
    }

    // Get all customer orders with details
    $orders = DB::table('orders')
        ->where('customer_id', $customer_id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $orderDetails = [];
    if ($orders->count()) {
        $orderIds = $orders->pluck('order_id');
        
        $orderDetails = DB::table('orderitem')
            ->join('product', 'orderitem.product_id', '=', 'product.product_id')
            ->join('sellers', 'product.seller_id', '=', 'sellers.seller_id')
            ->leftJoin('product_images', function($join) {
                $join->on('product.product_id', '=', 'product_images.product_id')
                     ->whereRaw('product_images.img_id = (SELECT MIN(img_id) FROM product_images WHERE product_images.product_id = product.product_id)');
            })
            ->whereIn('orderitem.order_id', $orderIds)
            ->select(
                'orderitem.*',
                'product.product_name',
                'product.product_id',
                'sellers.store_name as seller_name',
                'product_images.img_url as product_image',
                'orderitem.status as item_status'
            )
            ->get()
            ->groupBy('order_id');
    }

    // Get all customer reviews
    $reviews = DB::table('review')
        ->join('orders', 'review.order_id', '=', 'orders.order_id')
        ->join('orderitem', function($join) {
            $join->on('orders.order_id', '=', 'orderitem.order_id')
                 ->on('review.product_id', '=', 'orderitem.product_id');
        })
        ->join('product', 'review.product_id', '=', 'product.product_id')
        ->join('sellers', 'product.seller_id', '=', 'sellers.seller_id')
        ->leftJoin('product_images', 'product.product_id', '=', 'product_images.product_id')
        ->where('review.customer_id', $customer_id)
        ->select(
            'orders.order_id',
            'orders.created_at as order_date',
            'review.review_id',
            'review.rating',
            'review.comment as review_message',
            'review.created_at as review_date',
            'product.product_id',
            'product.product_name',
            'product_images.img_url as product_image',
            'sellers.store_name as seller_name'
        )
        ->orderBy('review.created_at', 'desc')
        ->get();

    return view('viewCustomerDetail', [
        'customer' => $customer,
        'orders' => $orders,
        'orderDetails' => $orderDetails,
        'reviews' => $reviews
    ]);
}

    public function productManagement(){
    $product = DB::table('product')->orderBy('product_id')->get();
    $category = DB::table('category')->orderBy('category_id')->get();
    $images = DB::table('product_images')->orderBy('img_id')->get();
    
    return view('productManagement', [
        'product' => $product,
        'category' => $category,
        'images' => $images,

    ]);
}

public function deactivateProduct_viewAllProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        if (!$product) {
            throw new \Exception('Product not found.');
        }
        DB::table('message')->insert([
                'admin_id' => Auth::id(),
                'seller_id' => $product->seller_id,
                'messages' => "Your product '{$product->product_name}' has been deactivated. Please contact admin for more information.",
                'is_read' => 0,
                'sender_type' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('product')
            ->where('product_id', $product_id)
            ->update(['product_status' => 'Deactivate']); // Consider using consistent status values
    });

    return redirect()->route('productManagement')
                    ->with('success', 'Product Deactivated successfully!');

}

public function activateProduct_viewAllProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        if (!$product) {
            throw new \Exception('Product not found.');
        }
        DB::table('message')->insert([
        'admin_id' => Auth::id(),
        'seller_id' => $product->seller_id,
        'messages' => "Your product '{$product->product_name}' has been aactivated. Thank you for your patience.",
        'is_read' => 0,
        'sender_type' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

        DB::table('product')
            ->where('product_id', $product_id)
            ->update(['product_status' => 'Activate']); // Consider using consistent status values
    });

    return redirect()->route('productManagement')
                    ->with('success', 'Product Activated successfully!');

}



    public function allAdminMessages()
{
    $adminId = Auth::id();
    
    $conversations = DB::table('message')
        ->join('sellers', 'message.seller_id', '=', 'sellers.seller_id')
        ->select(
            'sellers.seller_id',
            'sellers.full_name as seller_name',
            'sellers.seller_profile_img as seller_profile_img',
            DB::raw('MAX(message.created_at) as last_message_time'),
            DB::raw('(SELECT COUNT(*) FROM message WHERE seller_id = sellers.seller_id AND admin_id = '.$adminId.' AND is_read = 0 AND sender_type = "seller") as unread_count')
        )
        ->where('message.admin_id', $adminId)
        ->groupBy('sellers.seller_id', 'sellers.full_name', 'sellers.seller_profile_img')
        ->orderBy('last_message_time', 'desc')
        ->get();

    return view('allAdminMessages', [
        'conversations' => $conversations,
    ]);
}

public function adminMessageSeller($seller_id)
{
    $adminId = Auth::id();

    // Mark seller's messages as read
    DB::table('message')
        ->where('admin_id', $adminId)
        ->where('seller_id', $seller_id)
        ->where('sender_type', 'seller')
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

    $messages = DB::table('message')
        ->where('admin_id', $adminId)
        ->where('seller_id', $seller_id)
        ->orderBy('message_id', 'asc')
        ->get();

    $seller = DB::table('sellers')
        ->where('seller_id', $seller_id)
        ->first();

    return view('adminMessageSeller', [
        'messages' => $messages,
        'seller' => $seller,
    ]);
}

public function processAdminMessageToSeller(Request $request, $seller_id)
{
    $validated = $request->validate([
        'message' => 'required|string|max:500', 
    ]);

    $adminId = Auth::id();

    try {
        DB::table('message')->insert([
            'admin_id' => $adminId,
            'seller_id' => $seller_id,
            'messages' => $validated['message'],
            'is_read' => 0,
            'sender_type' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.message.seller', ['seller_id' => $seller_id])
               ->with('success', 'Message sent successfully.');
        
    } catch (\Exception $e) {
        return redirect()->route('adminMessageSeller', ['seller_id' => $seller_id])
               ->withInput()
               ->with('error', 'Failed to send message: ' . $e->getMessage());
    }
}


    

}
