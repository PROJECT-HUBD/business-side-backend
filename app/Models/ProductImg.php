<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImg extends Model
{
    use HasFactory;
    public $timestamps = false; // 停用 timestamps
    protected $table = 'product_img';
    protected $fillable = ['product_id', 'product_img_URL'];
}