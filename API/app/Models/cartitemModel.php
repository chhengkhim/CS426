<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cartitemModel extends Model
{
    protected $table = 'CartItem';
    protected $primaryKey = 'cart_item_id';
    protected $fillable = ['customer_id', 'product_id', 'quantity', 'added_at'];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(\App\Models\productModel::class, 'product_id', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\customersModel::class, 'customer_id', 'customer_id');
    }
}
