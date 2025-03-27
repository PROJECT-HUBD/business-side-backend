<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 使用者基本請求
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 商品管理
Route::get('/products', [ProductController::class, 'index']); // 取得商品列表
Route::post('/products', [ProductController::class, 'store']); // 新增商品
Route::get('/products/spec', [ProductController::class, 'productswithspec']); // 取得扁平化產品資料（用於市場行銷的適用商品） 
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
