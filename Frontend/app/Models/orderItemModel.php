<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orderItemModel extends Model
{
    protected $table = 'orderitem';
    protected $primaryKey = 'order_item_id';
    protected $fillable = ['order_id', 'product_id', 'seller_id', 'quantity', 'price_at_purchase', 'status'];
}

