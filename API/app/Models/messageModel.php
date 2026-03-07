<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class messageModel extends Model
{
    protected $table = 'message';
    protected $primaryKey = 'message_id';
    protected $fillable = [
        'customer_id',
        'seller_id',
        'admin_id',
        'subject',
        'messages',
        'sender_type',
        'is_read'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\customersModel::class, 'customer_id', 'customer_id');
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\sellerModel::class, 'seller_id', 'seller_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id', 'admin_id');
    }
}
