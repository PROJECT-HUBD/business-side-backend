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

// -------------------------------------------------------------------------
// 金流管理相關路由 - 不需要認證的 API
// -------------------------------------------------------------------------

// 金流管理儀表板
Route::get('/payments/dashboard', [PaymentController::class, 'dashboard']);
Route::get('/payments/chart-data', [PaymentController::class, 'getChartData']);

// 金流數據導出
Route::get('/payments/transactions/export', [PaymentController::class, 'exportTransactions']);
Route::get('/payments/reconciliations/export', [PaymentController::class, 'exportReconciliations']);
Route::get('/payments/export-excel', [PaymentController::class, 'exportExcel']);
Route::get('/payments/export-csv', [PaymentController::class, 'exportCsv']);

// 金流數據相關路由
Route::prefix('transactions')->group(function () {
    Route::get('/daily-summary', [PaymentController::class, 'getDailyTransactionsSummary']);
    Route::get('/daily/{date}', [PaymentController::class, 'getDailyTransactionDetail']);
    Route::post('/{transactionId}/note', [PaymentController::class, 'addOrderNote']);
    Route::get('/stats', [PaymentController::class, 'getDailyTransactionStats']);
    Route::get('/order/{orderId}', [PaymentController::class, 'getOrderDetail']);
    Route::get('/chart-data', [PaymentController::class, 'getChartData']);
});

// 對帳相關路由
Route::prefix('reconciliations')->group(function () {
    Route::get('/', [PaymentController::class, 'getDailyReconciliations']);
    Route::post('/daily/{date}', [PaymentController::class, 'reconcileDailyTransactions']);
    Route::post('/daily', [PaymentController::class, 'reconcileDailyTransactions']);
});

// 金流設定相關路由
Route::prefix('cash-flow-settings')->group(function () {
    Route::get('/', [CashFlowController::class, 'index']);
    Route::post('/', [CashFlowController::class, 'store']);
    Route::get('/{name}', [CashFlowController::class, 'show']);
    Route::put('/{name}', [CashFlowController::class, 'update']);
    Route::delete('/{name}', [CashFlowController::class, 'destroy']);
});

// -------------------------------------------------------------------------
// 需要認證的 API
// -------------------------------------------------------------------------
Route::middleware(['auth:sanctum'])->group(function () {
    // 支付相關路由（需要認證）
    Route::prefix('payments')->group(function () {
        Route::get('/daily-transactions', [PaymentController::class, 'getDailyTransactionsSummary']);
        Route::get('/daily-transaction-detail', [PaymentController::class, 'getDailyTransactionDetail']);
        Route::get('/order-detail/{orderId}', [PaymentController::class, 'getOrderDetailById']);
        Route::put('/update-reconciliation-status', [PaymentController::class, 'updateReconciliationStatus']);
    });
});
