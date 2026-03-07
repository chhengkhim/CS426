<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ordersModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $fillable = [
        'customer_id',
        'total_items',
        'total_amount',
        'shipping_address',
        'phone_number'
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\orderItemModel::class, 'order_id', 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\customersModel::class, 'customer_id', 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\reviewModel::class, 'order_id', 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\paymentModel::class, 'order_id', 'order_id');
    }
}
