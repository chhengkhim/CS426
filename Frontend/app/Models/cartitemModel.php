<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cartitemModel extends Model
{
    protected $table = 'CartItem';
    protected $primaryKey = 'cart_item_id';
    protected $fillable = ['customer_id', 'product_id', 'quantity', 'added_at'];
    public $timestamps = false;
}
