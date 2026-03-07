<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class productModel extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_name',
        'product_description',
        'product_price',
        'stock_quantity',
        'seller_id',
        'category_id'
    ];

    public function images()
    {
        return $this->hasMany(\App\Models\productimagesModel::class, 'product_id', 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\categoryModel::class, 'category_id', 'category_id');
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\sellerModel::class, 'seller_id', 'seller_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\reviewModel::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\orderItemModel::class, 'product_id', 'product_id');
    }
}
