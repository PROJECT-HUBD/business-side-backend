<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSpec;
use App\Models\ProductImg;
use App\Models\ProductInformation;
use App\Models\ProductDisplayImg; // 新增引用
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 取得商品列表
    public function index()
    {
        return response()->json(Product::with(['specifications', 'images', 'information', 'displayImages', 'classifiction'])->get());
    }

    // 取得單一商品
    // public function show($id)
    // {
    //     $product = Product::with(['specifications', 'images', 'information', 'displayImages', 'classifiction'])->find($id);
    //     if (!$product) {
    //         return response()->json(['error' => '商品不存在'], 404);
    //     }
    //     return response()->json($product);
    // }

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

        // ✅ 測試 Request 資料
        \Log::info('收到請求資料: ', $request->all());

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

        \Log::info("收到的子分類：" . $request->child_category);
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

        \Log::info('預存程序回傳: ', (array) $product);
        \Log::info("取得的新商品 ID: " . json_encode($product));
        // 取得新商品的 ID
        $newProductId = $product[0]->product_id ?? null;
        if (!$newProductId) {
            return response()->json(['error' => '商品新增失敗'], 500);
        }

        // ✅ 儲存商品規格
        $specifications = $request->input('specifications');

        if (!empty($specifications)) {
            \Log::info("解析後的規格: " . json_encode($specifications));
            foreach ($specifications as $spec) {
                // 生成唯一的 spec_id
                $specId = 'SPEC' . date('YmdHis') . rand(1000, 9999);
                
                // 確保 spec_id 不重複
                while (ProductSpec::where('spec_id', $specId)->exists()) {
                    $specId = 'SPEC' . date('YmdHis') . rand(1000, 9999);
                }

                ProductSpec::create([
                    'product_id' => $newProductId,
                    'spec_id' => $specId,
                    'product_size' => $spec['product_size'],
                    'product_color' => $spec['product_color'],
                    'product_stock' => $spec['product_stock'],
                ]);
            }
        }

        \Log::info("商品須知: ", [
            'material' => $request->material,
            'specification' => $request->specification,
            'shipping' => $request->shipping,
            'additional' => $request->additional
        ]);

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

        if ($request->hasFile('images')) {
            $imageFiles = $request->file('images');

            // 如果是單個文件，轉為陣列
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }

            $imageFolder = "products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";

            foreach ($imageFiles as $index => $image) {
                $imagePath = $image->storeAs($imageFolder, $image->getClientOriginalName(), 'public');
                \Log::info("商品圖片儲存: ", ['images' => $request->file('images')]);
                ProductImg::create([
                    'product_id' => $newProductId,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $request->input('product_name'),
                    'product_display_order' => $index + 1
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
                \Log::info("產品展示圖片儲存: ", ['display_images' => $request->file('display_images')]);
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

    // public static function insertProduct($childCategory, $product_name, $product_price, $product_description, $product_img, $product_status)
    // {
    //     $childCategory = trim($childCategory);
    //     $childCategory = mb_convert_encoding($childCategory, 'UTF-8', 'auto'); // 轉換編碼
    //     \Log::info("清理後的子分類：" . json_encode($childCategory));
    //     $procedureMap = [
    //         "異世界2000" => 'insert_product_pai',
    //         '水晶晶系列' => 'insert_product_pac',
    //         '長袖' => 'insert_product_pl',
    //         '短袖' => 'insert_product_ps'
    //     ];
    //     \Log::info("收到的子分類：" . $childCategory);
    //     \Log::info("可用的預存程序對應：" . json_encode($procedureMap));
    //     $procedure = $procedureMap[$childCategory] ?? null;
    //     \Log::info("即將呼叫的預存程序：" . $procedure);
    //     if (!$procedure) {
    //         return null;
    //     }

    //     return \DB::select("CALL {$procedure}(?, ?, ?, ?, ?)", [
    //         $product_name,
    //         $product_price,
    //         $product_description,
    //         $product_img,
    //         $product_status // ✅ 這裡加上 product_status
    //     ]);
    // }

    // 更新商品
    public function update(Request $request, $id)
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

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => '商品不存在'], 404);
        }

        \Log::info('更新商品資訊: ', $request->all());

        // 更新基本資訊
        $product->update([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
            'product_status' => $request->product_status
        ]);

        // 處理商品圖片
        if ($request->hasFile('images')) {
            // 先刪除原有圖片
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->product_img_URL);
            }
            ProductImg::where('product_id', $id)->delete();

            // 添加新圖片
            $imageFiles = $request->file('images');
            if (!is_array($imageFiles)) {
                $imageFiles = [$imageFiles];
            }

            $imageFolder = "products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";
            
            // 更新主圖
            if (count($imageFiles) > 0) {
                $firstImage = $imageFiles[0];
                $productImgPath = $firstImage->storeAs($imageFolder, $firstImage->getClientOriginalName(), 'public');
                $product->update([
                    'product_img' => str_replace('public/', '', $productImgPath)
                ]);
            }

            // 添加所有圖片
            foreach ($imageFiles as $index => $image) {
                $imagePath = $image->storeAs($imageFolder, $image->getClientOriginalName(), 'public');
                ProductImg::create([
                    'product_id' => $id,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $request->input('product_name'),
                    'product_display_order' => $index + 1
                ]);
            }
        }

        // 處理展示圖片
        if ($request->hasFile('display_images')) {
            // 先刪除原有展示圖
            ProductDisplayImg::where('product_id', $id)->delete();

            // 添加新展示圖
            $displayFiles = $request->file('display_images');
            if (!is_array($displayFiles)) {
                $displayFiles = [$displayFiles];
            }

            $displayFolder = "products_display/{$request->parent_category}/{$request->child_category}/{$request->product_name}";

            foreach ($displayFiles as $index => $image) {
                $imagePath = $image->storeAs($displayFolder, $image->getClientOriginalName(), 'public');
                ProductDisplayImg::create([
                    'product_id' => $id,
                    'product_img_URL' => str_replace('public/', '', $imagePath),
                    'product_alt_text' => $request->input('product_name'),
                    'product_display_order' => $index + 1
                ]);
            }
        }

        // 處理規格
        if ($request->has('specifications')) {
            // 先刪除原有規格
            ProductSpec::where('product_id', $id)->delete();
            
            // 添加新規格
            $specifications = $request->input('specifications');
            if (!empty($specifications)) {
                foreach ($specifications as $spec) {
                    // 生成唯一的 spec_id
                    $specId = 'SPEC' . date('YmdHis') . rand(1000, 9999);
                    
                    // 確保 spec_id 不重複
                    while (ProductSpec::where('spec_id', $specId)->exists()) {
                        $specId = 'SPEC' . date('YmdHis') . rand(1000, 9999);
                    }

                    ProductSpec::create([
                        'product_id' => $id,
                        'spec_id' => $specId,
                        'product_size' => $spec['product_size'],
                        'product_color' => $spec['product_color'],
                        'product_stock' => $spec['product_stock'],
                    ]);
                }
            }
        }

        // 處理商品須知
        if ($request->has('material') || $request->has('specification') || 
            $request->has('shipping') || $request->has('additional')) {
            
            // 先刪除原有商品須知
            ProductInformation::where('product_id', $id)->delete();
            
            // 添加新商品須知
            ProductInformation::create([
                'product_id' => $id,
                'title' => '材質',
                'content' => $request->material,
            ]);
            ProductInformation::create([
                'product_id' => $id,
                'title' => '規格',
                'content' => $request->specification,
            ]);
            ProductInformation::create([
                'product_id' => $id,
                'title' => '出貨說明',
                'content' => $request->shipping,
            ]);
            ProductInformation::create([
                'product_id' => $id,
                'title' => '其他補充',
                'content' => $request->additional,
            ]);
        }

        return response()->json(['message' => '商品更新成功', 'product_id' => $id]);
    }

    /**
     * 取得扁平化產品資料（用於市場行銷的適用商品）
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function productswithspec()
    {
        $products = Product::with(['specifications', 'images', 'information', 'displayImages', 'classifiction'])->get();
        
        $formattedProducts = [];
        
        foreach ($products as $product) {
            // 處理無規格的產品
            if ($product->specifications->isEmpty()) {
                $formattedProducts[] = [
                    'id' => $product->product_id,
                    'product_id' => $product->product_id,
                    'name' => $product->product_name,
                    'price' => $product->product_price,
                    'stock' => 0, // 無規格產品默認庫存
                    'image' => url('storage/' . $product->product_img),
                    'description' => $product->product_description,
                    'color' => null,
                    'size' => null,
                    'sku' => $product->product_id,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ];
            }
            
            // 處理有規格的產品
            foreach ($product->specifications as $spec) {
                $formattedProducts[] = [
                    'id' => $spec->spec_id,
                    'spec_id' => $spec->spec_id,
                    'product_id' => $product->product_id,
                    'main_product_id' => $product->product_id,
                    'name' => $product->product_name,
                    'price' => $product->product_price,
                    'stock' => $spec->product_stock,
                    'image' => url('storage/' . $product->product_img),
                    'description' => $product->product_description,
                    'color' => $spec->product_color,
                    'size' => $spec->product_size,
                    'sku' => $spec->spec_id,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at
                ];
            }
        }
        
        return response()->json($formattedProducts);
    }
}