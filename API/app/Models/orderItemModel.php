<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orderItemModel extends Model
{
    protected $table = 'orderitem';
    protected $primaryKey = 'order_item_id';
    protected $fillable = ['order_id', 'product_id', 'seller_id', 'quantity', 'price_at_purchase', 'status'];

    public function product()
    {
        return $this->belongsTo(\App\Models\productModel::class, 'product_id', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\ordersModel::class, 'order_id', 'order_id');
    }
}

