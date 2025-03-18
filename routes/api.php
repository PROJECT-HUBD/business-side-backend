<?php

use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\MarketingStatsController;
use App\Http\Controllers\API\CouponUsageController;
use App\Http\Controllers\API\CampaignParticipantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 優惠券路由
Route::get('/coupons', [CouponController::class, 'index']);
Route::post('/coupons', [CouponController::class, 'store']);
Route::put('/coupons/{id}', [CouponController::class, 'update']);
Route::get('/coupons/check-code/{code}', [CouponController::class, 'checkCode']);

// 優惠券使用記錄路由
Route::get('/coupons/{couponId}/usages', [CouponUsageController::class, 'index']);
Route::post('/coupons/{couponId}/usages', [CouponUsageController::class, 'store']);
Route::delete('/coupons/{couponId}/usages/{usageId}', [CouponUsageController::class, 'destroy']);
Route::get('/coupons/{couponId}/stats', [CouponUsageController::class, 'getStats']);

// 行銷活動路由
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::post('/campaigns', [CampaignController::class, 'store']);
Route::put('/campaigns/{id}', [CampaignController::class, 'update']);

// 活動參與記錄路由
Route::get('/campaigns/{campaignId}/participants', [CampaignParticipantController::class, 'index']);
Route::post('/campaigns/{campaignId}/participants', [CampaignParticipantController::class, 'store']);
Route::patch('/campaigns/{campaignId}/participants/{participantId}/status', [CampaignParticipantController::class, 'updateStatus']);
Route::delete('/campaigns/{campaignId}/participants/{participantId}', [CampaignParticipantController::class, 'destroy']);
Route::get('/campaigns/{campaignId}/stats', [CampaignParticipantController::class, 'getStats']);

// 行銷統計數據
Route::get('/marketing-stats', [MarketingStatsController::class, 'index']);
Route::get('/marketing-stats/monthly', [MarketingStatsController::class, 'monthlyStats']);
