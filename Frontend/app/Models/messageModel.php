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
        'messages',
        'is_read'
    ];
}
