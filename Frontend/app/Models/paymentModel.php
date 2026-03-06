<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class paymentModel extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    protected $fillable = ['order_id', 'payment_method', 'payment_status', 'paid_at']; 
}
