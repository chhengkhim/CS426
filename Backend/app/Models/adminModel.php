<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class adminModel extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
