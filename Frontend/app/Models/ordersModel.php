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
}
