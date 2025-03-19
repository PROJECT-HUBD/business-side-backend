<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/order', [OrderController::class, 'getOrderAll']);
Route::get('/orderdetail', [OrderController::class, 'getOrderDetails']);
Route::get('/orderWithDetails', [OrderController::class, 'getOrderWithDetails']);
Route::get('/order/{order_id}', [OrderController::class, 'getOrder']);

Route::get('/users', [UserController::class, 'index']); // 取得所有用戶
Route::get('/users/{id}', [UserController::class, 'show']); // 取得特定用戶
Route::get('/users/{id}/orders', [UserController::class, 'getUserOrders']);//獲取order_main資料


Route::get('/banners', [StoreController::class, 'index']);
Route::post('/banners/{id}', [StoreController::class, 'update']); // 確保這裡是 POST
