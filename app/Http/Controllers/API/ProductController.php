<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * 獲取所有產品清單
     */
    public function index()
    {
        // 從 product_spec 和 product_main 表格聯合獲取產品列表
        $products = DB::table('product_spec')
            ->join('product_main', 'product_spec.product_id', '=', 'product_main.product_id')
            ->select(
                'product_spec.spec_id as id', 
                'product_spec.spec_id',  // 添加原始spec_id
                'product_spec.product_id', // 添加product_id用於關聯
                'product_main.product_id as main_product_id', // 添加主商品ID用於分組
                'product_main.product_name as name', 
                'product_main.product_price as price', 
                'product_spec.spec_id as sku',
                'product_spec.product_stock as stock',
                'product_main.product_img as image',
                'product_main.product_description as description',
                'product_spec.product_color as color',
                'product_spec.product_size as size',
                'product_main.created_at',
                'product_main.updated_at'
            )
            ->orderBy('product_main.product_name', 'asc')
            ->get();
        
        // 格式化資料
        foreach ($products as $product) {
            // 如果價格是數字，格式化為兩位小數
            if (is_numeric($product->price)) {
                $product->price = number_format((float)$product->price, 0, '.', '');
            }
            
            // 確保圖片URL是完整的
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                // 在開發環境中，可能需要替換為您的實際URL
                $product->image = url('storage/' . $product->image);
            }
        }
        
        return response()->json($products);
    }
    
    /**
     * 獲取特定產品
     */
    public function show($id)
    {
        $product = DB::table('product_spec')
            ->join('product_main', 'product_spec.product_id', '=', 'product_main.product_id')
            ->select(
                'product_spec.spec_id as id', 
                'product_spec.spec_id',  // 添加原始spec_id
                'product_spec.product_id', // 添加product_id用於關聯
                'product_main.product_id as main_product_id', // 添加主商品ID用於分組
                'product_main.product_name as name', 
                'product_main.product_price as price', 
                'product_spec.spec_id as sku',
                'product_spec.product_stock as stock',
                'product_main.product_img as image',
                'product_main.product_description as description',
                'product_spec.product_color as color',
                'product_spec.product_size as size',
                'product_main.created_at',
                'product_main.updated_at'
            )
            ->where('product_spec.spec_id', $id)
            ->first();
        
        if (!$product) {
            return response()->json(['message' => '產品不存在'], 404);
        }
        
        // 如果價格是數字，格式化為兩位小數
        if (is_numeric($product->price)) {
            $product->price = number_format((float)$product->price, 0, '.', '');
        }
        
        // 確保圖片URL是完整的
        if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
            // 在開發環境中，可能需要替換為您的實際URL
            $product->image = url('storage/' . $product->image);
        }
        
        return response()->json($product);
    }
} 