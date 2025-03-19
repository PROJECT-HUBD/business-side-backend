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
        return response()->json(Product::with(['specifications', 'images', 'information'])->get());
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

        // ✅ 測試 Request 資料
        \Log::info('收到請求資料: ', $request->all());

        $productImgPath = null;
        if ($request->hasFile('images')) {
            $imageFolder = "public/products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";
            $images = $request->file('images');

            // 確保 images 是陣列
            if (!is_array($images)) {
                $images = [$images]; // 轉成陣列
            }

            // 取得第一張圖片
            $firstImage = $images[0];
            $productImgPath = $firstImage->storeAs($imageFolder, $firstImage->getClientOriginalName());
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
                ProductSpec::create([
                    'product_id' => $newProductId,
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

            $imageFolder = "public/products/{$request->parent_category}/{$request->child_category}/{$request->product_name}";

            foreach ($imageFiles as $index => $image) {
                $imagePath = $image->storeAs($imageFolder, $image->getClientOriginalName());
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

            $displayFolder = "public/products_display/{$request->parent_category}/{$request->child_category}/{$request->product_name}";

            foreach ($displayFiles as $index => $image) {
                $imagePath = $image->storeAs($displayFolder, $image->getClientOriginalName());
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
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => '商品不存在'], 404);
        }

        $product->update($request->only([
            'product_name',
            'category_id',
            'product_price',
            'product_description',
            'product_status'
        ]));

        return response()->json($product);
    }
}