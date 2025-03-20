<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductImg;
use Illuminate\Support\Facades\Log;

class ProductImagesController extends Controller
{
    /**
     * 獲取指定產品的所有圖片
     *
     * @param string $productId 產品ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductImages($productId)
    {
        try {
            Log::info("獲取產品圖片請求", ['product_id' => $productId]);
            
            // 根據產品ID查詢所有相關圖片，按照顯示順序排序
            $images = ProductImg::where('product_id', $productId)
                ->orderBy('product_display_order', 'asc')
                ->get();
            
            // 調整返回的字段名，確保與前端期望的一致
            $formattedImages = $images->map(function($img) {
                return [
                    'id' => $img->id,
                    'product_id' => $img->product_id,
                    'product_img_url' => $img->product_img_URL,
                    'product_img_URL' => $img->product_img_URL, // 提供兩種大小寫格式，確保兼容性
                    'product_alt_text' => $img->product_alt_text,
                    'product_display_order' => $img->product_display_order,
                    'created_at' => $img->created_at,
                    'updated_at' => $img->updated_at
                ];
            });
            
            Log::info("產品圖片查詢結果", [
                'product_id' => $productId, 
                'image_count' => $images->count(),
                'images' => $formattedImages
            ]);
            
            // 返回格式化後的圖片數據
            return response()->json($formattedImages);
        } catch (\Exception $e) {
            Log::error("獲取產品圖片失敗", [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => '獲取產品圖片失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 更新圖片顯示順序
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateImagesOrder(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string',
                'images' => 'required|array',
                'images.*.id' => 'required|integer',
                'images.*.product_display_order' => 'required|integer'
            ]);
            
            $productId = $request->product_id;
            $imagesData = $request->images;
            
            Log::info("更新圖片順序請求", [
                'product_id' => $productId,
                'images_count' => count($imagesData)
            ]);
            
            foreach ($imagesData as $imageData) {
                $image = ProductImg::find($imageData['id']);
                if ($image && $image->product_id === $productId) {
                    $image->product_display_order = $imageData['product_display_order'];
                    $image->save();
                    
                    Log::info("更新圖片順序", [
                        'image_id' => $image->id,
                        'new_order' => $imageData['product_display_order']
                    ]);
                }
            }
            
            // 獲取更新後的圖片
            $updatedImages = ProductImg::where('product_id', $productId)
                ->orderBy('product_display_order', 'asc')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => '圖片順序已更新',
                'data' => $updatedImages
            ]);
        } catch (\Exception $e) {
            Log::error("更新圖片順序失敗", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => '更新圖片順序失敗: ' . $e->getMessage()
            ], 500);
        }
    }
} 