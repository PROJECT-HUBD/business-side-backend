<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Campaign;
use App\Models\CouponUsage;
use App\Models\CampaignParticipant;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketingStatsController extends Controller
{
    /**
     * 獲取行銷統計數據概覽
     */
    public function index()
    {
        $now = Carbon::now();
        
        // 統計優惠券
        $activeCoupons = Coupon::where('status', 'active')
            ->where(function($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now->toDateString());
            })
            ->count();
            
        $expiredCoupons = Coupon::where('end_date', '<', $now->toDateString())->count();
        
        $scheduledCoupons = Coupon::where('status', 'active')
            ->where('start_date', '>', $now->toDateString())
            ->count();
            
        $totalCoupons = Coupon::count();
        
        // 統計行銷活動
        $activeCampaigns = Campaign::where('status', 'active')
            ->where('start_date', '<=', $now->toDateString())
            ->where('end_date', '>=', $now->toDateString())
            ->count();
            
        $expiredCampaigns = Campaign::where('end_date', '<', $now->toDateString())->count();
        
        $scheduledCampaigns = Campaign::where('status', 'active')
            ->where('start_date', '>', $now->toDateString())
            ->count();
            
        $totalCampaigns = Campaign::count();
        
        // 統計使用情況
        $totalCouponUsages = Coupon::sum('usage_count');
        $totalCouponAmount = CouponUsage::sum('discount_amount');
        
        $totalCampaignParticipants = Campaign::sum('redemption_count');
        $totalCampaignAmount = CampaignParticipant::where('status', 'completed')->sum('discount_amount');
        
        // 回傳統計數據
        return response()->json([
            'coupons' => [
                'total' => $totalCoupons,
                'active' => $activeCoupons,
                'expired' => $expiredCoupons,
                'scheduled' => $scheduledCoupons,
                'usage_count' => $totalCouponUsages,
                'discount_amount' => $totalCouponAmount
            ],
            'campaigns' => [
                'total' => $totalCampaigns,
                'active' => $activeCampaigns,
                'expired' => $expiredCampaigns,
                'scheduled' => $scheduledCampaigns,
                'participant_count' => $totalCampaignParticipants,
                'discount_amount' => $totalCampaignAmount
            ]
        ]);
    }
    
    /**
     * 獲取詳細的月度統計數據
     */
    public function monthlyStats(Request $request)
    {
        // 預設為當前月份
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        // 構建月份的開始和結束日期
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
        
        // 優惠券每日使用統計
        $couponDailyStats = CouponUsage::whereBetween('used_at', [$startDate, $endDate])
            ->selectRaw('DATE(used_at) as date, COUNT(*) as count, SUM(discount_amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 活動每日參與統計
        $campaignDailyStats = CampaignParticipant::where('status', 'completed')
            ->whereBetween('joined_at', [$startDate, $endDate])
            ->selectRaw('DATE(joined_at) as date, COUNT(*) as count, SUM(discount_amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 熱門優惠券
        $popularCoupons = CouponUsage::whereBetween('used_at', [$startDate, $endDate])
            ->select('coupon_id', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(discount_amount) as total_amount'))
            ->groupBy('coupon_id')
            ->orderByDesc('usage_count')
            ->limit(5)
            ->with('coupon:id,title,code')
            ->get();
        
        // 熱門活動
        $popularCampaigns = CampaignParticipant::where('status', 'completed')
            ->whereBetween('joined_at', [$startDate, $endDate])
            ->select('campaign_id', DB::raw('COUNT(*) as participant_count'), DB::raw('SUM(discount_amount) as total_amount'))
            ->groupBy('campaign_id')
            ->orderByDesc('participant_count')
            ->limit(5)
            ->with('campaign:id,name,type')
            ->get();
        
        // 總計
        $totalStats = [
            'coupon_usage_count' => CouponUsage::whereBetween('used_at', [$startDate, $endDate])->count(),
            'coupon_discount_amount' => CouponUsage::whereBetween('used_at', [$startDate, $endDate])->sum('discount_amount'),
            'campaign_participant_count' => CampaignParticipant::where('status', 'completed')->whereBetween('joined_at', [$startDate, $endDate])->count(),
            'campaign_discount_amount' => CampaignParticipant::where('status', 'completed')->whereBetween('joined_at', [$startDate, $endDate])->sum('discount_amount'),
        ];
        
        return response()->json([
            'period' => [
                'year' => (int)$year,
                'month' => (int)$month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'daily_stats' => [
                'coupons' => $couponDailyStats,
                'campaigns' => $campaignDailyStats,
            ],
            'popular' => [
                'coupons' => $popularCoupons,
                'campaigns' => $popularCampaigns,
            ],
            'totals' => $totalStats,
        ]);
    }
} 