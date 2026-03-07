<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class sellerModel extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     */
    protected $table = 'sellers';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'seller_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'seller_email',
        'password',
        'store_name',
        'seller_address',
        'phone_number',
        'seller_profile_img',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    public function products()
    {
        return $this->hasMany(\App\Models\productModel::class, 'seller_id', 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\messageModel::class, 'seller_id', 'seller_id');
    }
}
