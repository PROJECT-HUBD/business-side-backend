<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product_main';
    protected $primaryKey = 'product_id';
    public $incrementing = false; // ✅ 這裡不能用 Laravel 自動生成 ID，因為 ID 是 MySQL 預存程序產生的
    protected $keyType = 'string';

    protected $fillable = [
        'product_name', 'category_id', 'product_price', 'product_description', 'product_status'
    ];

    // **關聯商品規格**
    public function specifications()
    {
        return $this->hasMany(ProductSpec::class, 'product_id', 'product_id');
    }

    // **關聯商品圖片**
    public function images()
    {
        return $this->hasMany(ProductImg::class, 'product_id', 'product_id');
    }

    // **關聯產品展示圖**
    public function displayImages()
    {
        return $this->hasMany(ProductDisplayImg::class, 'product_id', 'product_id');
    }

    // **關聯商品須知**
    public function information()
    {
        return $this->hasMany(ProductInformation::class, 'product_id', 'product_id');
    }

    // **使用預存程序插入商品**
    public static function insertProduct($category, $product_name, $product_price, $product_description, $product_img, $product_status)
    {
        $procedure = match ($category) {
            '飾品' => 'insert_product_pa',
            '長袖' => 'insert_product_pl',
            '短袖' => 'insert_product_ps',
            default => null
        };

        if (!$procedure) {
            return null;
        }

        return \DB::select("CALL {$procedure}(?, ?, ?, ?, ?)", [
            $product_name,
            $product_price,
            $product_description,
            $product_img,
            $product_status
        ]);
    }
}