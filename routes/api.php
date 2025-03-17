<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/order', [OrderController::class, 'getOrderAll']);
Route::get('/orderdetail', [OrderController::class, 'getOrderDetails']);
Route::get('/orderWithDetails', [OrderController::class, 'getOrderWithDetails']);
Route::get('/order/{order_id}', [OrderController::class, 'getOrder']);
