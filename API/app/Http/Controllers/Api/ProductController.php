<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\productModel;
use App\Models\categoryModel;
use App\Models\productimagesModel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = productModel::with(['category', 'images'])->get();

        $result = $products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name ?? $product->product_name,
                'product_description' => $product->description ?? $product->product_description,
                'product_price' => $product->price ?? $product->product_price,
                'category_name' => $product->category->name ?? $product->category->category_name ?? null,
                'img_url' => $product->images->first()->img_url ?? null,
            ];
        });

        return response()->json($result);
    }
} 