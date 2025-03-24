<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CouponUsageController extends Controller
{
    /**
     * 獲取指定優惠券的使用記錄
     */
    public function index($couponId)
    {
        $coupon = Coupon::findOrFail($couponId);
        $usages = $coupon->usages()->with('user')->latest()->get();
        
        return response()->json($usages);
    }
    
    /**
     * 記錄優惠券使用
     */
    public function store(Request $request, $couponId)
    {
        $coupon = Coupon::findOrFail($couponId);
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'order_id' => 'nullable|integer',
            'discount_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // 檢查優惠券是否可用
        if (!$coupon->isAvailable()) {
            return response()->json(['error' => '優惠券不可用'], 400);
        }
        
        // 檢查使用次數限制
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return response()->json(['error' => '已達使用次數上限'], 400);
        }
        
        // 創建使用記錄
        $usage = new CouponUsage([
            'coupon_id' => $couponId,
            'user_id' => $request->user_id,
            'order_id' => $request->order_id,
            'discount_amount' => $request->discount_amount,
            'used_at' => Carbon::now(),
            'note' => $request->note,
        ]);
        
        $usage->save();
        
        // 更新優惠券使用次數
        $coupon->increment('usage_count');
        
        return response()->json($usage, 201);
    }
    
    /**
     * 刪除優惠券使用記錄
     */
    public function destroy($couponId, $usageId)
    {
        $coupon = Coupon::findOrFail($couponId);
        $usage = CouponUsage::where('coupon_id', $couponId)->where('id', $usageId)->firstOrFail();
        
        $usage->delete();
        
        // 更新優惠券使用次數
        if ($coupon->usage_count > 0) {
            $coupon->decrement('usage_count');
        }
        
        return response()->json(['message' => '使用記錄已刪除']);
    }
    
    /**
     * 獲取優惠券使用統計
     */
    public function getStats($couponId)
    {
        // 暫時關閉 ONLY_FULL_GROUP_BY 限制
        DB::statement("SET SQL_MODE=''");
        
        $coupon = Coupon::findOrFail($couponId);
        
        $totalUsage = $coupon->usage_count;
        $totalAmount = $coupon->usages()->sum('discount_amount');
        
        $dailyStats = $coupon->usages()
            ->selectRaw('DATE(used_at) as date, COUNT(*) as count, SUM(discount_amount) as amount')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        
        // 恢復正常的 SQL_MODE
        DB::statement("SET SQL_MODE=(SELECT @@sql_mode)");
        
        return response()->json([
            'total_usage' => $totalUsage,
            'total_discount_amount' => $totalAmount,
            'daily_stats' => $dailyStats
        ]);
    }
} 