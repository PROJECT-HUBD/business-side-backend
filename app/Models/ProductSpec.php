<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpec extends Model
{
    use HasFactory;

    protected $table = 'product_spec';
    protected $fillable = ['product_id', 'product_size', 'product_color', 'product_stock', 'spec_id'];
}