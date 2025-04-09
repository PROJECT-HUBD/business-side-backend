<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 先執行優惠券和行銷活動的 Seeder
        $this->call([
            CouponSeeder::class,
            CampaignSeeder::class,
            // 金流相關的 Seeder
            PaymentTransactionSeeder::class,
            PaymentReconciliationSeeder::class,
            TransactionSeeder::class,
        ]);
        
        // 創建測試用戶（如果不存在）
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
