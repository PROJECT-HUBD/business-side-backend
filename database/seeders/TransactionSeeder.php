<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('zh_TW');
        
        // 決定要產生的數據日期範圍 (最近30天)
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // 支付方式列表
        $paymentMethods = ['credit_card', 'bank_transfer', 'line_pay', 'cash'];
        
        // 交易狀態列表
        $transactionStatus = ['completed', 'pending', 'failed', 'refunded'];
        
        // 產品列表
        $products = [
            ['name' => '基本會員月費', 'price' => 299],
            ['name' => '進階會員月費', 'price' => 499],
            ['name' => '專業會員年費', 'price' => 3999],
            ['name' => '諮詢服務基本套餐', 'price' => 1200],
            ['name' => '諮詢服務進階套餐', 'price' => 2500],
            ['name' => '專業顧問服務', 'price' => 5000],
            ['name' => '單次講座入場券', 'price' => 350],
            ['name' => '講座系列票券', 'price' => 1500],
            ['name' => '數位課程 - 基礎', 'price' => 699],
            ['name' => '數位課程 - 進階', 'price' => 1299],
        ];
        
        // 確保 order_detail 表存在
        if (!Schema::hasTable('order_detail')) {
            // 如果表格不存在，創建表格
            Schema::create('order_detail', function (Blueprint $table) {
                $table->id();
                $table->string('order_id')->comment('訂單編號');
                $table->string('product_name')->comment('產品名稱');
                $table->decimal('product_price', 10, 2)->comment('產品價格');
                $table->integer('quantity')->default(1)->comment('數量');
                $table->timestamps();
                
                $table->index('order_id');
            });
        }
        
        // 每天生成2-8筆交易
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $transactionsPerDay = rand(2, 8);
            
            // 對前15天的交易標記為已對帳
            $isReconciled = $currentDate < Carbon::now()->subDays(15);
            $reconciliationDate = $isReconciled ? Carbon::now()->subDays(rand(1, 14)) : null;
            $reconciliationStatus = $isReconciled ? 'completed' : null;
            $reconciliationNotes = $isReconciled ? '系統自動對帳' : null;
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // 隨機決定是否為退款 (10%機率)
                $isRefund = rand(1, 10) === 1;
                
                // 隨機選擇產品
                $selectedProduct = $faker->randomElement($products);
                $productName = $selectedProduct['name'];
                $productPrice = $selectedProduct['price'];
                
                // 生成訂單編號和交易編號
                $orderId = 'ORD' . $currentDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                $tradeNo = 'TRX' . $currentDate->format('Ymd') . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                
                // 決定交易時間 (當天隨機時間)
                $transactionTime = clone $currentDate;
                $transactionTime->setTime(rand(8, 22), rand(0, 59), rand(0, 59));
                
                // 決定交易金額
                $amount = $isRefund ? -$productPrice : $productPrice;
                
                // 決定手續費 (約2-3%)
                $feeRate = rand(20, 30) / 1000;
                $feeAmount = round(abs($amount) * $feeRate, 2);
                
                // 決定交易狀態 (退款固定為refunded，其他95%為completed)
                $status = $isRefund ? 'refunded' : (rand(1, 100) <= 95 ? 'completed' : $faker->randomElement(['pending', 'failed']));
                
                // 隨機決定付款方式
                $paymentMethod = $faker->randomElement($paymentMethods);
                
                // 隨機決定是否有備註 (5%機率)
                $hasNote = rand(1, 20) === 1;
                $note = $hasNote ? $faker->sentence() : null;
                
                // 插入訂單記錄
                $mainOrderId = DB::table('order_main')->insertGetId([
                    'order_id' => $orderId,
                    'trade_No' => $tradeNo,
                    'trade_Date' => $transactionTime,
                    'total_price_with_discount' => $amount,
                    'payment_type' => $paymentMethod,
                    'trade_status' => $status,
                    'reconciliation_status' => $reconciliationStatus,
                    'reconciliation_notes' => $reconciliationNotes,
                    'reconciliation_date' => $reconciliationDate,
                    'fee_amount' => $feeAmount,
                    'notes' => $note,
                    'created_at' => $transactionTime,
                    'updated_at' => $transactionTime,
                ]);
                
                // 插入訂單詳情
                DB::table('order_detail')->insert([
                    'order_id' => $orderId,
                    'product_name' => $productName,
                    'product_price' => $productPrice,
                    'quantity' => 1,
                    'created_at' => $transactionTime,
                    'updated_at' => $transactionTime,
                ]);
            }
            
            // 如果該日交易已對帳，創建對帳記錄
            if ($isReconciled) {
                // 獲取該日交易統計
                $dailyStats = DB::table('order_main')
                    ->selectRaw('COUNT(*) as transaction_count')
                    ->selectRaw('SUM(total_price_with_discount) as total_amount')
                    ->whereDate('trade_Date', $currentDate->format('Y-m-d'))
                    ->first();
                
                // 插入對帳記錄
                DB::table('reconciliations')->insert([
                    'reconciliation_number' => 'REC' . $currentDate->format('Ymd'),
                    'reconciliation_date' => $currentDate->format('Y-m-d'),
                    'transaction_count' => $dailyStats->transaction_count,
                    'total_amount' => $dailyStats->total_amount,
                    'staff_name' => '系統',
                    'notes' => '系統自動對帳',
                    'created_at' => $reconciliationDate,
                    'updated_at' => $reconciliationDate,
                ]);
            }
            
            $currentDate->addDay();
        }
    }
} 