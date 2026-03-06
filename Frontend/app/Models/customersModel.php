<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;

class customersModel extends Authenticatable

{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'customer_profile_images',
        'full_name',
        'age',
        'gender',
        'phone_number',
        'customers_email',
        'password',
        'account_status',
    ];
}
