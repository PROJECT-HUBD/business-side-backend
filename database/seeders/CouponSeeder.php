<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空現有資料
        Coupon::truncate();
        
        // 當前日期
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $nextMonth = Carbon::now()->addMonth();
        $nextWeek = Carbon::now()->addWeek();
        
        // 活動優惠券
        Coupon::create([
            'title' => '新會員首單9折',
            'code' => 'WELCOME10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_purchase' => 100,
            'start_date' => $lastMonth->format('Y-m-d'),
            'end_date' => $nextMonth->format('Y-m-d'),
            'usage_limit' => 1,
            'description' => '新會員首次訂單可享9折優惠，最低消費金額$100',
            'status' => 'active',
            'can_combine' => false,
            'users' => null,
            'products' => null,
            'categories' => null,
            'usage_count' => 0
        ]);
        
        // 折扣優惠券 - 已過期
        Coupon::create([
            'title' => '春季特賣8折',
            'code' => 'SPRING20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'min_purchase' => 200,
            'start_date' => $lastMonth->subMonths(2)->format('Y-m-d'),
            'end_date' => $lastMonth->format('Y-m-d'),
            'usage_limit' => null,
            'description' => '春季特賣活動，全場商品8折',
            'status' => 'disabled',
            'can_combine' => false,
            'users' => null,
            'products' => null, 
            'categories' => null,
            'usage_count' => 45
        ]);
        
        // 固定金額折扣 - 活躍
        Coupon::create([
            'title' => '滿$500折$50',
            'code' => 'SAVE50',
            'discount_type' => 'fixed',
            'discount_value' => 50,
            'min_purchase' => 500,
            'start_date' => $lastMonth->format('Y-m-d'),
            'end_date' => $nextMonth->format('Y-m-d'),
            'usage_limit' => null,
            'description' => '消費滿$500，立即折抵$50',
            'status' => 'active',
            'can_combine' => true,
            'users' => null,
            'products' => null,
            'categories' => json_encode([1, 2, 3]),
            'usage_count' => 12
        ]);
        
        // 免運費優惠
        Coupon::create([
            'title' => '全站免運費',
            'code' => 'FREESHIP',
            'discount_type' => 'shipping',
            'discount_value' => null,
            'min_purchase' => 800,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $nextMonth->format('Y-m-d'),
            'usage_limit' => null,
            'description' => '訂單滿$800，享免運費優惠',
            'status' => 'active',
            'can_combine' => true,
            'users' => null,
            'products' => null,
            'categories' => null,
            'usage_count' => 8
        ]);
        
        // 買一送一優惠
        Coupon::create([
            'title' => '指定商品買一送一',
            'code' => 'BUY1GET1',
            'discount_type' => 'buy_x_get_y',
            'buy_quantity' => 1,
            'free_quantity' => 1,
            'min_purchase' => null,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $nextWeek->format('Y-m-d'),
            'usage_limit' => 1,
            'description' => '指定商品買一送一優惠',
            'status' => 'active',
            'can_combine' => false,
            'users' => null,
            'products' => json_encode([101, 102, 103]),
            'categories' => null,
            'usage_count' => 3
        ]);
        
        // 會員專屬優惠
        Coupon::create([
            'title' => 'VIP會員85折',
            'code' => 'VIP15',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'min_purchase' => 300,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $nextMonth->addMonths(2)->format('Y-m-d'),
            'usage_limit' => 3,
            'description' => 'VIP會員專屬85折優惠',
            'status' => 'active',
            'can_combine' => false,
            'users' => json_encode([1, 2, 3, 4, 5]),
            'products' => null,
            'categories' => null,
            'usage_count' => 0
        ]);
        
        // 未來活動優惠
        Coupon::create([
            'title' => '週年慶全館75折',
            'code' => 'ANNIV25',
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'min_purchase' => 500,
            'start_date' => $nextMonth->format('Y-m-d'),
            'end_date' => $nextMonth->addWeeks(2)->format('Y-m-d'),
            'usage_limit' => null,
            'description' => '週年慶期間全館商品75折',
            'status' => 'active',
            'can_combine' => false,
            'users' => null,
            'products' => null,
            'categories' => null,
            'usage_count' => 0
        ]);
    }
}
