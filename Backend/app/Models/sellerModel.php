<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class sellerModel extends Authenticatable
{
    protected $table = 'sellers';
    protected $primaryKey = 'seller_id';
    protected $fillable = [
        'seller_profile_img',
        'full_name',
        'seller_email',
        'password',
        'store_name',
        'seller_address',
        'phone_number',
        'account_status',
    ];
}
