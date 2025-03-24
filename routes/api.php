<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 商品管理
Route::get('/products', [ProductController::class, 'index']); // 取得商品列表
Route::post('/products', [ProductController::class, 'store']); // 新增商品
Route::put('/products/{id}', [ProductController::class, 'update']); // 更新商品
Route::get('/products/{id}', [ProductController::class, 'show']); // 取得單一商品

// 訂單管理
Route::get('/order', [OrderController::class, 'getOrderAll']);
Route::get('/orderdetail', [OrderController::class, 'getOrderDetails']);
Route::get('/orderWithDetails', [OrderController::class, 'getOrderWithDetails']);
Route::get('/order/{order_id}', [OrderController::class, 'getOrder']);

// 用戶管理
Route::get('/users', [UserController::class, 'index']); // 取得所有用戶
Route::get('/users/{id}', [UserController::class, 'show']); // 取得特定用戶
Route::get('/users/{id}/orders', [UserController::class, 'getUserOrders']);//獲取order_main資料

// 賣場管理
Route::get('/banners', [StoreController::class, 'index']);
Route::post('/banners/{id}', [StoreController::class, 'update']); // 確保這裡是 POST

// 維護管理
Route::get('/maintenance', [MaintenanceController::class, 'index']);
Route::post('/maintenance', [MaintenanceController::class, 'store']);
Route::delete('/maintenance', [MaintenanceController::class, 'destroy']);

// 維護管理
Route::get('/maintenance', [MaintenanceController::class, 'indexMaintenance']);
Route::post('/maintenance', [MaintenanceController::class, 'storeMaintenance']);
Route::delete('/maintenance', [MaintenanceController::class, 'destroyMaintenance']);
