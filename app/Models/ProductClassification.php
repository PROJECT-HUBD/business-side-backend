<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductClassification extends Model
{
    use HasFactory;

    protected $table = 'product_classification';
    protected $fillable = ['category_id', 'parent_category', 'child_category'];
}