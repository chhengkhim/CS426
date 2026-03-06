<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categoryModel extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];
    public $timestamps = false;
}
