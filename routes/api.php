<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// 訂單管理
Route::get('/order', [OrderController::class, 'getOrderAll']);
Route::get('/orderdetail', [OrderController::class, 'getOrderDetails']);
Route::get('/orderWithDetails', [OrderController::class, 'getOrderWithDetails']);
Route::get('/order/{order_id}', [OrderController::class, 'getOrder']);


// 維護管理
Route::get('/maintenance', [MaintenanceController::class, 'indexMaintenance']);
Route::post('/maintenance', [MaintenanceController::class, 'storeMaintenance']);
Route::delete('/maintenance', [MaintenanceController::class, 'destroyMaintenance']);
