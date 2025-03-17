<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\orderController;

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