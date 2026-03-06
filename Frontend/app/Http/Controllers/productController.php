<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\productModel; 
use App\Models\categoryModel;
use App\Models\sellerModel;
use App\Models\productimagesModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class productController extends Controller
{
    public function addProduct(){

        $category = DB::select("select * from category order by category_id asc");
        
        return view('addProduct', [
        'category' => $category
    ]);
    }

    public function process_addProduct(Request $request){
        
        // Validate input including the image file
        $validated = $request->validate([
    'product_name' => 'required|string',
    'product_description' => 'required|string',
    'product_price' => 'required|numeric',
    'stock_quantity' => 'required|integer',
    'category_name' => 'required|string',
    'other_category' => 'nullable|string',
    'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
]);

    $categoryName = $validated['category_name'] === '__other__'
    ? $validated['other_category']
    : $validated['category_name'];

    $category = categoryModel::firstOrCreate([
        'category_name' => $categoryName,
    ]);


    $product = new productModel();
    $product->product_name = $validated['product_name'];
    $product->product_description = $validated['product_description'];
    $product->product_price = $validated['product_price'];
    $product->stock_quantity = $validated['stock_quantity'];
    $product->seller_id = Auth::id(); // 👈 use the logged-in seller
    $product->category_id = $category->category_id;
    $product->save();
    // Save to DB

    // If image exists, store and save it
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('Assets/images', 'public');
        $url = Storage::url($path);

        $image = new productimagesModel();
        $image->img_url = $url;
        $image->product_id = $product->product_id;
        $image->save(); // Save to DB
    }

    
    return redirect('seller_Home')->with('success', 'Product added successfully!');

}


    public function Home()
    {
        $product = DB::select("select * from product where seller_id = ? order by product_id asc", [Auth::id()]);

        $sellerImages  = DB::select("select * from sellers where seller_id = ?", [Auth::id()]);

        $category = DB::select("select * from category order by category_id asc");

        $images = DB::select("select * from product_images order by img_id asc");
        

        return view('seller_Home', [
            'product' => $product,
            'category' => $category,
            'images' => $images,
            'sellerImages' => $sellerImages,
        ]);
    }


    public function updateProduct($product_id)
    {
        $product = DB::select("select * from product where seller_id = ? and product_id = ?", [Auth::id(), $product_id]);

        $category = DB::select("select * from category order by category_id asc");

        $images = DB::select("select * from product_images where product_id = ?", [$product_id]);
        

        return view('updateProduct', [
            'product' => $product,
            'category' => $category,
            'images' => $images,
        ]);
    }

    public function process_updateProduct(Request $request)
{
    $product_id = $request->input('product_id');

    // Validate the request
    $validated = $request->validate([
        'product_name' => 'required|string',
        'product_description' => 'required|string',
        'product_price' => 'required|numeric',
        'stock_quantity' => 'required|integer',
        'category_name' => 'required|string',
        'other_category' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    // Handle "Other" category
    $categoryName = $validated['category_name'] === '__other__'
        ? $validated['other_category']
        : $validated['category_name'];

    // Create or get the category
    $category = categoryModel::firstOrCreate([
        'category_name' => $categoryName,
    ]);

    // Find product
    $product = productModel::find($product_id);

    // Check if product exists and belongs to current seller
    if (!$product || $product->seller_id !== Auth::id()) {
        return redirect('seller_Home')->withErrors(['error' => 'Product not found or unauthorized.']);
    }

    // Update product fields
    $product->product_name = $validated['product_name'];
    $product->product_description = $validated['product_description'];
    $product->product_price = $validated['product_price'];
    $product->stock_quantity = $validated['stock_quantity'];
    $product->category_id = $category->category_id;
    $product->save();

    // Handle image upload
    if ($request->hasFile('image')) {
        // Delete old image if it exists
        $existingImage = productimagesModel::where('product_id', $product->product_id)->first();

        if ($existingImage) {
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $existingImage->img_url))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $existingImage->img_url));
            }

            $existingImage->delete();
        }

        // Upload new image
        $path = $request->file('image')->store('Assets/images', 'public');
        $url = '/storage/' . $path;

        // Use save() instead of create()
        $newImage = new productimagesModel();
        $newImage->product_id = $product->product_id;
        $newImage->img_url = $url;
        $newImage->save();
    }

    return redirect('seller_Home')->with('success', 'Product updated successfully!');
}

    public function deleteProduct($product_id)
{
    DB::transaction(function () use ($product_id) {
        $product = productModel::find($product_id);

        if (!$product || $product->seller_id !== Auth::id()) {
            throw new \Exception('Product not found or unauthorized.');
        }

        // Check if product has any pending orders
        $pendingOrders = DB::table('orderitem')
            ->where('product_id', $product_id)
            ->whereNotIn('status', ['received', 'cancelled'])
            ->exists();

        if ($pendingOrders) {
            return back()->withErrors('Cannot delete product. There are pending orders for this product.');
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

    return redirect('seller_Home')->with('success', 'Product deleted successfully!');
}


}