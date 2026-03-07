<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class customersModel extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     */
    protected $table = 'customers';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'customers_email',
        'password',
        'age',
        'gender',
        'phone_number',
        'customer_profile_images',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the email address associated with the user.
     * This is needed because the column is not named 'email'.
     */
    public function getEmailForPasswordReset()
    {
        return $this->customers_email;
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\ordersModel::class, 'customer_id', 'customer_id');
    }

    public function cartItems()
    {
        return $this->hasMany(\App\Models\cartitemModel::class, 'customer_id', 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\reviewModel::class, 'customer_id', 'customer_id');
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\messageModel::class, 'customer_id', 'customer_id');
    }
}
