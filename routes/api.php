<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\CashFlowController;

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

// 金流管理路由
Route::get('/payments/dashboard', [PaymentController::class, 'dashboard']);
Route::get('/payments/transactions', [PaymentController::class, 'getTransactions']);
Route::get('/payments/daily/{date}', [PaymentController::class, 'getDailyTransactions']);
Route::post('/payments/reconciliation/{date}', [PaymentController::class, 'updateReconciliation']);
Route::get('/payments/export-csv', [PaymentController::class, 'exportCsv']);
Route::get('/payments/export-excel', [PaymentController::class, 'exportExcel']);

// 金流設定相關路由
Route::prefix('cash-flow-settings')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\CashFlowController@index');
    Route::post('/', 'App\Http\Controllers\API\CashFlowController@store');
    Route::get('/{name}', 'App\Http\Controllers\API\CashFlowController@show');
    Route::put('/{name}', 'App\Http\Controllers\API\CashFlowController@update');
    Route::delete('/{name}', 'App\Http\Controllers\API\CashFlowController@destroy');
});

// 金流數據相關路由
Route::prefix('transactions')->group(function () {
    Route::get('/daily-summary', 'App\Http\Controllers\API\PaymentController@getDailyTransactionsSummary');
    Route::get('/daily/{date}', 'App\Http\Controllers\API\PaymentController@getDailyTransactionDetail');
    Route::post('/{transactionId}/note', 'App\Http\Controllers\API\PaymentController@addOrderNote');
    Route::get('/stats', 'App\Http\Controllers\API\PaymentController@getDailyTransactionStats');
    Route::get('/export-excel', 'App\Http\Controllers\API\PaymentController@exportExcel');
    Route::get('/export-csv', 'App\Http\Controllers\API\PaymentController@exportCsv');
    Route::get('/order/{orderId}', 'App\Http\Controllers\API\PaymentController@getOrderDetail');
    Route::get('/chart-data', 'App\Http\Controllers\API\PaymentController@getChartData');
});

// 對帳相關路由
Route::prefix('reconciliations')->group(function () {
    Route::get('/', 'App\Http\Controllers\API\PaymentController@getDailyReconciliations');
    Route::post('/daily/{date}', 'App\Http\Controllers\API\PaymentController@reconcileDailyTransactions');
});
