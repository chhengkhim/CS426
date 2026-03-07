<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reviewModel extends Model
{
    use HasFactory;

    protected $table = 'review';
    protected $primaryKey = 'review_id';

    protected $fillable = [
        'customer_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\customersModel::class, 'customer_id', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\productModel::class, 'product_id', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\ordersModel::class, 'order_id', 'order_id');
    }
}
