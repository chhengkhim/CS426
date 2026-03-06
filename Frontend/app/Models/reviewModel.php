<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reviewModel extends Model
{
    protected $table = 'review';
    protected $primaryKey = 'review_id';
    protected $fillable = ['customer_id', 'product_id','order_id', 'rating', 'comment'];
}
