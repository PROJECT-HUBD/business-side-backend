<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImagesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']); // 取得商品列表
Route::post('/products', [ProductController::class, 'store']); // 新增商品
Route::put('/products/{id}', [ProductController::class, 'update']); // 更新商品
Route::get('/products/{id}', [ProductController::class, 'show']); // 取得單一商品
Route::get('/products/{product_id}/images', [ProductImagesController::class, 'getProductImages']);
Route::post('/products/images/update-order', [ProductImagesController::class, 'updateImagesOrder']);

// 產品相關路由
Route::resource('products', ProductController::class);
Route::get('products/{id}/images', [ProductController::class, 'getProductImages']);

// 產品分類相關路由