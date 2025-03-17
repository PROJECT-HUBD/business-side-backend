<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']); // 取得商品列表
Route::post('/products', [ProductController::class, 'store']); // 新增商品
Route::put('/products/{id}', [ProductController::class, 'update']); // 更新商品
Route::get('/products/{id}', [ProductController::class, 'show']); // 取得單一商品