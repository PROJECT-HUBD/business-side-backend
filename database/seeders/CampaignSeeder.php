<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use Carbon\Carbon;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空現有資料
        Campaign::truncate();
        
        // 當前日期
        $now = Carbon::now();
        $yesterday = Carbon::now()->subDay();
        $lastMonth = Carbon::now()->subMonth();
        $nextMonth = Carbon::now()->addMonth();
        $nextWeek = Carbon::now()->addWeek();
        $twoWeeks = Carbon::now()->addWeeks(2);
        
        // 一般折扣活動 - 活躍
        Campaign::create([
            'name' => '春季新品85折',
            'type' => 'discount',
            'discount_method' => 'percentage',
            'discount_value' => 15,
            'start_date' => $lastMonth->format('Y-m-d'),
            'end_date' => $nextMonth->format('Y-m-d'),
            'description' => '春季新品上市，全館85折優惠',
            'status' => 'active',
            'can_combine' => false,
            'applicable_products' => null,
            'applicable_categories' => json_encode([1, 2]),
            'users' => null,
            'stock_limit' => null,
            'per_user_limit' => null,
            'redemption_count' => 35
        ]);
        
        // 買X送Y活動 - 活躍
        Campaign::create([
            'name' => '文具專區買二送一',
            'type' => 'buy_x_get_y',
            'buy_quantity' => 2,
            'free_quantity' => 1,
            'start_date' => $yesterday->format('Y-m-d'),
            'end_date' => $nextWeek->format('Y-m-d'),
            'description' => '文具專區指定商品，買二送一',
            'status' => 'active',
            'can_combine' => false,
            'applicable_products' => json_encode([201, 202, 203, 204, 205]),
            'applicable_categories' => null,
            'users' => null,
            'stock_limit' => 100,
            'per_user_limit' => 2,
            'redemption_count' => 8
        ]);
        
        // 組合優惠 - 活躍
        Campaign::create([
            'name' => '電腦配件超值組合',
            'type' => 'bundle',
            'bundle_quantity' => 3,
            'bundle_discount' => 20,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $twoWeeks->format('Y-m-d'),
            'description' => '購買指定電腦配件三件以上，享8折優惠',
            'status' => 'active',
            'can_combine' => false,
            'applicable_products' => json_encode([301, 302, 303, 304, 305]),
            'applicable_categories' => null,
            'users' => null,
            'stock_limit' => null,
            'per_user_limit' => null,
            'redemption_count' => 3
        ]);
        
        // 限時特賣 - 即將開始
        Campaign::create([
            'name' => '週末快閃5折',
            'type' => 'flash_sale',
            'flash_sale_discount' => 50,
            'flash_sale_start_time' => $nextWeek->format('Y-m-d').' 12:00:00',
            'flash_sale_end_time' => $nextWeek->addDays(2)->format('Y-m-d').' 23:59:59',
            'start_date' => $nextWeek->subDays(2)->format('Y-m-d'),
            'end_date' => $nextWeek->addDays(2)->format('Y-m-d'),
            'description' => '週末限時特賣，指定商品5折起',
            'status' => 'active',
            'can_combine' => false,
            'applicable_products' => json_encode([401, 402, 403]),
            'applicable_categories' => null,
            'users' => null,
            'stock_limit' => 50,
            'per_user_limit' => 1,
            'redemption_count' => 0
        ]);
        
        // 免運費活動 - 已過期
        Campaign::create([
            'name' => '新年免運優惠',
            'type' => 'free_shipping',
            'start_date' => $lastMonth->subMonths(2)->format('Y-m-d'),
            'end_date' => $lastMonth->format('Y-m-d'),
            'description' => '新年期間，全館訂單免運費',
            'status' => 'disabled',
            'can_combine' => true,
            'applicable_products' => null,
            'applicable_categories' => null,
            'users' => null,
            'stock_limit' => null,
            'per_user_limit' => null,
            'redemption_count' => 120
        ]);
        
        // 會員專屬折扣 - 未來
        Campaign::create([
            'name' => 'VIP會員專屬優惠',
            'type' => 'discount',
            'discount_method' => 'percentage',
            'discount_value' => 25,
            'start_date' => $nextMonth->format('Y-m-d'),
            'end_date' => $nextMonth->addMonths(2)->format('Y-m-d'),
            'description' => 'VIP會員專屬，全館商品75折',
            'status' => 'active',
            'can_combine' => false,
            'applicable_products' => null,
            'applicable_categories' => null,
            'users' => json_encode([1, 2, 3, 4, 5]),
            'stock_limit' => null,
            'per_user_limit' => 5,
            'redemption_count' => 0
        ]);
        
        // 特定分類活動 - 活躍
        Campaign::create([
            'name' => '家電優惠週',
            'type' => 'discount',
            'discount_method' => 'fixed',
            'discount_value' => 500,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $twoWeeks->format('Y-m-d'),
            'description' => '家電類別商品滿3000折500',
            'status' => 'active',
            'can_combine' => true,
            'applicable_products' => null,
            'applicable_categories' => json_encode([5]),
            'users' => null,
            'stock_limit' => null,
            'per_user_limit' => null,
            'redemption_count' => 12
        ]);
    }
}
