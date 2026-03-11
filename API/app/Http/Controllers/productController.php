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
    public function addProduct(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'product_name' => 'required|string',
            'product_description' => 'required|string',
            'product_price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'category_name' => 'required|string', // Expecting category name as per your original controller
        ]);

        // Find or create the category
        $category = \App\Models\categoryModel::firstOrCreate(
            ['category_name' => $validated['category_name']]
        );

        // Create the product
        $product = new \App\Models\productModel();
        $product->product_name = $validated['product_name'];
        $product->product_description = $validated['product_description'];
        $product->product_price = $validated['product_price'];
        $product->stock_quantity = $validated['stock_quantity'];
        $product->seller_id = Auth::id(); // Get the authenticated seller's ID
        $product->category_id = $category->category_id;
        $product->save();

        // Return the newly created product as a JSON response
        return response()->json($product, 201);
    }

    public function process_addProduct(Request $request)
    {

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
            $path = $request->file('image')->storePublicly('Assets/products', 'spaces');
            $url = Storage::disk('spaces')->url($path);

            $image = new productimagesModel();
            $image->img_url = $url;
            $image->product_id = $product->product_id;
            $image->save(); // Save to DB
        }


        return response()->json(['message' => 'Product added successfully!'], 201);
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


    public function updateProduct(Request $request, $id)
    {
        // Find the product that belongs to the authenticated seller to ensure
        // a seller can only update their own products.
        $product = \App\Models\productModel::where('product_id', $id)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        // Validate only the fields that are sent in the request
        $validated = $request->validate([
            'product_name' => 'sometimes|string|max:255',
            'product_description' => 'sometimes|string',
            'product_price' => 'sometimes|numeric',
            'stock_quantity' => 'sometimes|integer',
        ]);

        // Update the product with the validated data
        $product->update($validated);

        // Return a success response with the updated product data
        return response()->json($product);
    }

    public function process_updateProduct(Request $request, $id)
    {
        $product_id = $id;

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
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            if ($product->seller_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized to update this product'], 403);
            }
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
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
                // Extract the object key from the full Spaces URL
                $parsedUrl = parse_url($existingImage->img_url);
                $objectKey = ltrim($parsedUrl['path'] ?? '', '/');

                if ($objectKey && Storage::disk('spaces')->exists($objectKey)) {
                    Storage::disk('spaces')->delete($objectKey);
                }

                $existingImage->delete();
            }

            // Upload new image to DigitalOcean Spaces
            $path = $request->file('image')->storePublicly('Assets/products', 'spaces');
            $url = Storage::disk('spaces')->url($path);

            // Use save() instead of create()
            $newImage = new productimagesModel();
            $newImage->product_id = $product->product_id;
            $newImage->img_url = $url;
            $newImage->save();
        }

        return response()->json(['message' => 'Product updated successfully!'], 200);
    }

    public function deleteProduct($id)
    {
        // Find the product that belongs to the authenticated seller.
        // This is a security measure to ensure a seller can only delete their own products.
        $product = \App\Models\productModel::where('product_id', $id)
            ->where('seller_id', Auth::id())
            ->first();

        // If the product doesn't exist or doesn't belong to the seller, return a 404 error.
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Delete the product.
        $product->delete();

        // Return a success response. 204 No Content is standard for a successful delete.
        return response()->json("product deleted successfully", 204);
    }

    // API: List all products for the seller
    public function listSellerProducts()
    {
        // Example: Fetch products for the authenticated seller
        $products = productModel::where('seller_id', Auth::id())->get();
        return response()->json(['products' => $products]);
    }

    // API: List all products for customers
    public function listAllProducts()
    {
        $products = \App\Models\productModel::with(['images', 'category', 'seller', 'reviews'])->get();
        return response()->json($products);
    }

    public function customer_product_detail($id)
    {
        $product = \App\Models\productModel::with(['images', 'category', 'seller', 'reviews'])->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }
}
