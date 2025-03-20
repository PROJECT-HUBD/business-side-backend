<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\ProductImg;
use App\Models\ProductInformation;
use App\Models\ProductDisplayImg; // 新增引用
use Illuminate\Support\Facades\Storage;
use App\Models\ProductClassification;

class ProductController extends Controller
{
    // 取得商品列表
    public function index()
    {
        return response()->json(Product::with(['specifications', 'images', 'information', 'displayImages', 'classifiction'])->get());
    }

    // 取得單一商品
    public function show($id)
    {
        $product = Product::with(['specifications', 'images', 'information'])->find($id);
        if (!$product) {
            return response()->json(['error' => '商品不存在'], 404);
        }
        return response()->json($product);
    }

    // 新增商品
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'parent_category' => 'required|string',
            'child_category' => 'required|string',
            'product_price' => 'required|numeric',
            'product_description' => 'nullable|string',
            'product_status' => 'required|string',
            'specifications' => 'nullable|array',
            'material' => 'nullable|string',
            'specification' => 'nullable|string',
            'shipping' => 'nullable|string',
            'additional' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'display_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);



        $productImgPath = null;
        if ($request->hasFile('images')) {
            $imageFolder = "products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";
            $images = $request->file('images');

            // 確保 images 是陣列
            if (!is_array($images)) {
                $images = [$images]; // 轉成陣列
            }

            // 取得第一張圖片
            $firstImage = $images[0];
            $productImgPath = $firstImage->storeAs($imageFolder, $firstImage->getClientOriginalName(), 'public');
            $productImgPath = str_replace('public/', '', $productImgPath); // 移除 public 路徑
        }

        // ✅ 呼叫預存程序來插入商品，並填入 product_img
        $product = Product::insertProduct(
            $request->child_category, // ✅ 傳入子分類
            $request->product_name,
            $request->product_price,
            $request->product_description,
            $productImgPath, // ✅ 使用第一張圖片作為 `product_img`
            $request->product_status // 傳入狀態
        );

        if (!$product) {
            return response()->json(['error' => '分類錯誤，無法新增商品'], 400);
        }

 
        // 取得新商品的 ID
        $newProductId = $product[0]->product_id ?? null;
        if (!$newProductId) {
            return response()->json(['error' => '商品新增失敗'], 500);
        }

        // ✅ 儲存商品規格
        $specifications = $request->input('specifications');

        if (!empty($specifications)) {
            \Log::info('開始處理商品規格', ['規格數量' => count($specifications)]);
            
            foreach ($specifications as $spec) {
                // 使用與update相同的邏輯創建規格
                $specId = $this->updateOrCreateProductSpec(
                    $newProductId,
                    $spec['product_size'],
                    $spec['product_color'],
                    $spec['product_stock']
                );
                
                \Log::info('創建新規格', [
                    'product_id' => $newProductId,
                    'spec_id' => $specId,
                    'size' => $spec['product_size'],
                    'color' => $spec['product_color'],
                    'stock' => $spec['product_stock']
                ]);
            }
        }

       

        // ✅ 儲存商品須知
        ProductInformation::create([
            'product_id' => $newProductId,
            'title' => '材質',
            'content' => $request->material,
        ]);
        ProductInformation::create([
            'product_id' => $newProductId,
            'title' => '規格',
            'content' => $request->specification,
        ]);
        ProductInformation::create([
            'product_id' => $newProductId,
            'title' => '出貨說明',
            'content' => $request->shipping,
        ]);
        ProductInformation::create([
            'product_id' => $newProductId,
            'title' => '其他補充',
            'content' => $request->additional,
        ]);

        // 處理新增商品的圖片上傳
        if ($request->hasFile('images')) {
            $imageFiles = $request->file('images');

            // 如果是單個文件，轉為陣列
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }

            
            $imageFolder = "products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";
            
            foreach ($imageFiles as $index => $image) {
                $imagePath = $image->storeAs($imageFolder, $image->getClientOriginalName(),'public');
                
                // 獲取圖片順序，如果有傳入
                $orderIndex = $request->input("images_order.{$index}", $index + 1);
                
                ProductImg::create([
                    'product_id' => $newProductId,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $request->input('product_name'),
                    'product_display_order' => $orderIndex
                ]);
                
                \Log::info("新增商品圖片", [
                    'product_id' => $newProductId,
                    'image_path' => str_replace('public/', '', $imagePath),
                    'order' => $orderIndex
                ]);
            }
        }

        if ($request->hasFile('display_images')) {
            $displayFiles = $request->file('display_images');

            // 如果是單個文件，轉為陣列
            if (!is_array($displayFiles)) {
                $displayFiles = [$displayFiles];
            }

            $displayFolder = "products_display/{$request->parent_category}/{$request->child_category}/{$request->product_name}";

            foreach ($displayFiles as $index => $image) {
                $imagePath = $image->storeAs($displayFolder, $image->getClientOriginalName(), 'public');

                ProductDisplayImg::create([
                    'product_id' => $newProductId,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $request->input('product_name'),
                    'product_display_order' => $index + 1
                ]);
            }
        }




        return response()->json(['message' => '商品新增成功', 'product_id' => $newProductId], 201);
    }

    public static function insertProduct($childCategory, $product_name, $product_price, $product_description, $product_img, $product_status)
    {
        $procedureMap = [
            '異世界2000' => 'insert_product_pa',
            '水晶晶系列' => 'insert_product_pa',
            '長袖' => 'insert_product_pl',
            '短袖' => 'insert_product_ps'
        ];

        $procedure = $procedureMap[$childCategory] ?? null;

        if (!$procedure) {
            return null;
        }

        return \DB::select("CALL {$procedure}(?, ?, ?, ?, ?)", [
            $product_name,
            $product_price,
            $product_description,
            $product_img,
            $product_status // ✅ 這裡加上 product_status
        ]);
    }

    // 更新商品
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => '商品不存在'], 404);
        }

        $request->validate([
            'product_name' => 'required|string',
            'parent_category' => 'nullable|string',
            'child_category' => 'nullable|string',
            'product_price' => 'required|numeric',
            'product_description' => 'nullable|string',
            'product_status' => 'required|string',
            'specifications' => 'nullable|array',
            'material' => 'nullable|string',
            'specification' => 'nullable|string',
            'shipping' => 'nullable|string',
            'additional' => 'nullable|string',
            'images' => 'nullable',
            'display_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 更新商品基本資料
        $product->update([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
            'product_status' => $request->product_status
        ]);

        // 處理商品規格
        $specifications = $request->input('specifications');
        if (!empty($specifications)) {
            // 先記錄所有已存在的規格
            $existingSpecs = ProductSpec::where('product_id', $id)->get();
            $updated = []; // 記錄已更新的規格ID
            
            foreach ($specifications as $spec) {
                // 嘗試更新或創建規格
                $specId = $this->updateOrCreateProductSpec(
                    $id,
                    $spec['product_size'],
                    $spec['product_color'],
                    $spec['product_stock']
                );
                
                if ($specId) {
                    $updated[] = $specId;
                    \Log::info('更新或創建規格成功', ['spec_id' => $specId]);
                }
            }
            
            // 刪除不再使用的規格（未在本次更新中包含的舊規格）
            foreach ($existingSpecs as $oldSpec) {
                if (!in_array($oldSpec->spec_id, $updated)) {
                    \Log::info('刪除未使用的規格', ['spec_id' => $oldSpec->spec_id]);
                    $oldSpec->delete();
                }
            }
        }

        // 處理商品須知更新
        if ($request->has('material')) {
            $this->updateOrCreateProductInfo($id, '材質', $request->material);
        }
        if ($request->has('specification')) {
            $this->updateOrCreateProductInfo($id, '規格', $request->specification);
        }
        if ($request->has('shipping')) {
            $this->updateOrCreateProductInfo($id, '出貨說明', $request->shipping);
        }
        if ($request->has('additional')) {
            $this->updateOrCreateProductInfo($id, '其他補充', $request->additional);
        }

        // 處理商品圖片
        if ($request->has('images')) {
            \Log::info('處理圖片更新', [
                'request_images' => $request->images,
                'has_files' => $request->hasFile('images'),
                'files_count' => count($request->allFiles())
            ]);
            
            // 取得產品分類資訊，用於構建儲存路徑
            $category = ProductClassification::where('category_id', $product->category_id)->first();
            $parentCategory = $category ? $category->parent_category : 'uncategorized';
            $childCategory = $category ? $category->child_category : 'uncategorized';
            
            // 圖片資料夾路徑
            $imageFolder = "products/{$parentCategory}/{$childCategory}/{$product->product_name}";
            
            // 檢查request中所有文件
            $allFiles = [];
            foreach ($request->allFiles() as $key => $value) {
                $allFiles[$key] = $value;
            }
            \Log::info('請求中的所有文件', ['allFiles' => array_keys($allFiles)]);
            
            // 檢查是否是JSON格式的圖片資料
            if (is_string($request->images)) {
                try {
                    $imagesData = json_decode($request->images, true);
                    \Log::info('解析圖片JSON資料', ['imagesData' => $imagesData]);
                    
                    // 建立一個已處理圖片的ID列表，用於之後清理未使用的圖片
                    $processedImageIds = [];
                    
                    // 處理每個圖片
                    foreach ($imagesData as $index => $imageData) {
                        $hasNewFile = isset($imageData['has_new_file']) && $imageData['has_new_file'];
                        \Log::info("處理圖片 {$index}", [
                            'has_new_file' => $hasNewFile, 
                            'imageData' => $imageData,
                            'display_order' => $imageData['product_display_order'] ?? ($index + 1)
                        ]);
                        
                        // 檢查是否有對應的文件上傳
                        $fileKey = "images.{$index}.file";
                        $uploadedFile = $request->file($fileKey);
                        
                        if ($uploadedFile) {
                            \Log::info("處理新上傳的文件: {$fileKey}", ['file_name' => $uploadedFile->getClientOriginalName()]);
                            // 上傳新檔案
                            $imagePath = $uploadedFile->storeAs($imageFolder, $uploadedFile->getClientOriginalName(), 'public');
                            $imagePath = str_replace('public/', '', $imagePath);
                            
                            // 確保使用正確的顯示順序
                            $displayOrder = isset($imageData['product_display_order']) ? 
                                $imageData['product_display_order'] : 
                                ($index + 1);
                                
                            // 如果有提供圖片ID，則更新，否則新增
                            if (isset($imageData['id'])) {
                                $img = ProductImg::find($imageData['id']);
                                if ($img) {
                                    $img->update([
                                        'product_img_URL' => $imagePath,
                                        'product_alt_text' => $product->product_name,
                                        'product_display_order' => $displayOrder
                                    ]);
                                    $processedImageIds[] = $img->id;
                                    \Log::info("更新現有圖片", ['id' => $img->id, 'new_path' => $imagePath, 'order' => $displayOrder]);
                                }
                            } else {
                                $newImg = ProductImg::create([
                                    'product_id' => $id,
                                    'product_img_URL' => $imagePath,
                                    'product_alt_text' => $product->product_name,
                                    'product_display_order' => $displayOrder
                                ]);
                                $processedImageIds[] = $newImg->id;
                                \Log::info("新增圖片", ['id' => $newImg->id, 'path' => $imagePath, 'order' => $displayOrder]);
                            }
                        } 
                        // 處理現有圖片（無新文件上傳）
                        else if (isset($imageData['id'])) {
                            $img = ProductImg::find($imageData['id']);
                            if ($img) {
                                // 只更新顯示順序，確保使用正確的索引
                                $displayOrder = isset($imageData['product_display_order']) ? 
                                    $imageData['product_display_order'] : 
                                    ($index + 1);
                                    
                                \Log::info("更新圖片順序", [
                                    'id' => $img->id, 
                                    'old_order' => $img->product_display_order,
                                    'new_order' => $displayOrder
                                ]);
                                
                                $img->update([
                                    'product_display_order' => $displayOrder
                                ]);
                                
                                $processedImageIds[] = $img->id;
                            }
                        }
                    }
                    
                    // 清理未在本次請求中包含的舊圖片
                    $unusedImages = ProductImg::where('product_id', $id)
                        ->whereNotIn('id', $processedImageIds)
                        ->get();
                    
                    foreach ($unusedImages as $unusedImg) {
                        \Log::info("刪除未使用的圖片", ['id' => $unusedImg->id, 'path' => $unusedImg->product_img_URL]);
                        $unusedImg->delete();
                    }
                    
                    // 更新所有保留的圖片順序，確保按照前端提供的順序保存
                    $allProductImages = ProductImg::where('product_id', $id)->get();
                    \Log::info("更新後的所有圖片:", $allProductImages->map(function($img) {
                        return [
                            'id' => $img->id,
                            'url' => $img->product_img_URL,
                            'order' => $img->product_display_order
                        ];
                    })->toArray());
                    
                } catch (\Exception $e) {
                    \Log::error('解析圖片JSON失敗', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                }
            } else {
                // 原始處理邏輯，如果不是JSON格式
                $files = $request->file('images');
                if (is_array($files)) {
                    foreach ($files as $index => $file) {
                        if ($file) {
                            $imagePath = $file->storeAs($imageFolder, $file->getClientOriginalName(), 'public');
                            $imagePath = str_replace('public/', '', $imagePath);
                            
                            $newImg = ProductImg::create([
                                'product_id' => $id,
                                'product_img_URL' => $imagePath,
                                'product_alt_text' => $product->product_name,
                                'product_display_order' => $index + 1
                            ]);
                            \Log::info("新增圖片(非JSON模式)", ['id' => $newImg->id, 'path' => $imagePath]);
                        }
                    }
                }
            }
            
            // 更新產品主圖
            $mainImage = ProductImg::where('product_id', $id)
                ->orderBy('product_display_order', 'asc')
                ->first();
                
            if ($mainImage) {
                $product->update(['product_img' => $mainImage->product_img_URL]);
                \Log::info("更新產品主圖", ['product_id' => $id, 'main_image' => $mainImage->product_img_URL]);
            }
        }

        // 處理展示圖片（如果有）
        if ($request->hasFile('display_images')) {
            $displayFiles = $request->file('display_images');
            
            // 如果是單個文件，轉為陣列
            if (!is_array($displayFiles)) {
                $displayFiles = [$displayFiles];
            }
            
            // 取得分類資訊
            $category = ProductClassification::where('category_id', $product->category_id)->first();
            $parentCategory = $category ? $category->parent_category : 'uncategorized';
            $childCategory = $category ? $category->child_category : 'uncategorized';
            
            $displayFolder = "products_display/{$parentCategory}/{$childCategory}/{$product->product_name}";
            
            foreach ($displayFiles as $index => $image) {
                $imagePath = $image->storeAs($displayFolder, $image->getClientOriginalName(), 'public');
                
                ProductDisplayImg::create([
                    'product_id' => $id,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $product->product_name,
                    'product_display_order' => $index + 1
                ]);
            }
        }

        return response()->json(['message' => '商品更新成功', 'product' => $product->load(['specifications', 'images', 'information', 'displayImages'])]);
    }
    
    // 更新或創建商品規格
    private function updateOrCreateProductSpec($productId, $size, $color, $stock)
    {
        // 查找是否存在相符的規格
        $spec = ProductSpec::where('product_id', $productId)
            ->where('product_size', $size)
            ->where('product_color', $color)
            ->first();
            
        if ($spec) {
            // 更新已存在的規格
            $spec->update([
                'product_stock' => $stock,
            ]);
            \Log::info('更新規格', [
                'product_id' => $productId,
                'spec_id' => $spec->spec_id,
                'size' => $size,
                'color' => $color,
                'stock' => $stock
            ]);
            return $spec->spec_id;
        } else {
            // 创建新規格
            $newSpec = ProductSpec::create([
                'product_id' => $productId,
                'product_size' => $size,
                'product_color' => $color,
                'product_stock' => $stock,
            ]);
            \Log::info('新增規格', [
                'product_id' => $productId,
                'spec_id' => $newSpec->spec_id,
                'size' => $size,
                'color' => $color,
                'stock' => $stock
            ]);
            return $newSpec->spec_id;
        }
    }
    
    // 更新或創建商品須知
    private function updateOrCreateProductInfo($productId, $title, $content)
    {
        $info = ProductInformation::where('product_id', $productId)
            ->where('title', $title)
            ->first();
            
        if ($info) {
            // 使用where條件而不是依賴id來更新
            ProductInformation::where('product_id', $productId)
                ->where('title', $title)
                ->update(['content' => $content]);
                
            \Log::info('更新商品須知', [
                'product_id' => $productId,
                'title' => $title
            ]);
        } else {
            ProductInformation::create([
                'product_id' => $productId,
                'title' => $title,
                'content' => $content
            ]);
            
            \Log::info('創建商品須知', [
                'product_id' => $productId,
                'title' => $title
            ]);
        }
    }

    /**
     * 獲取指定產品的所有圖片
     */
    public function getProductImages($productId)
    {
        try {
            \Log::info("獲取產品圖片", ['product_id' => $productId]);
            
            $images = ProductImg::where('product_id', $productId)
                ->orderBy('product_display_order', 'asc')
                ->get();
                
            \Log::info("找到產品圖片", ['count' => $images->count()]);
            
            // 數據轉換，確保所有URL字段統一
            $formattedImages = $images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'product_id' => $img->product_id,
                    'product_img_url' => $img->product_img_URL, // 保持一致的小寫URL
                    'product_img_URL' => $img->product_img_URL, // 原始大寫URL
                    'product_alt_text' => $img->product_alt_text,
                    'product_display_order' => $img->product_display_order,
                    'created_at' => $img->created_at,
                    'updated_at' => $img->updated_at
                ];
            });
            
            return response()->json($formattedImages);
        } catch (\Exception $e) {
            \Log::error("獲取產品圖片出錯", [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => '無法獲取產品圖片',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}