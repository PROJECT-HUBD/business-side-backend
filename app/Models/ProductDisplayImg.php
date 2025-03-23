<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDisplayImg extends Model
{
    use HasFactory;
    public $timestamps = false; // 停用 timestamps
    protected $table = 'product_display_img';
    protected $fillable = ['product_id', 'product_img_URL', 'product_alt_text' ,'product_display_order'];
}