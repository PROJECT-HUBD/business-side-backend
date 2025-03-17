<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInformation extends Model
{
    use HasFactory;
    public $timestamps = false; // 停用 timestamps
    protected $table = 'product_infomation';
    protected $fillable = ['product_id', 'title', 'content'];
}