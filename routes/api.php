<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\DashboardController;

// 使用者基本請求
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 會員路由
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// 用於測試的路由，添加隨機生日
Route::post('/users/add-random-birthdays', [UserController::class, 'addRandomBirthdays']);

// 產品路由
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// 分類路由
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// 優惠券路由
Route::get('/coupons', [CouponController::class, 'index']);
Route::get('/coupons/{id}', [CouponController::class, 'show']);
Route::post('/coupons', [CouponController::class, 'store']);
Route::put('/coupons/{id}', [CouponController::class, 'update']);
Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);
Route::get('/coupons/check-code/{code}', [CouponController::class, 'checkCode']);

// 行銷活動路由
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/{id}', [CampaignController::class, 'show']);
Route::post('/campaigns', [CampaignController::class, 'store']);
Route::put('/campaigns/{id}', [CampaignController::class, 'update']);
Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

// 儀表板路由
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/dashboard/recent-orders', [DashboardController::class, 'getRecentOrders']);
Route::get('/dashboard/sales-chart', [DashboardController::class, 'getSalesChart']);
Route::get('/dashboard/product-stats', [DashboardController::class, 'getProductStats']);
