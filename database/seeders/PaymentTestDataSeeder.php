<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 支付方式列表
        $paymentMethods = ['credit_card', 'line_pay', 'bank_transfer', 'ecpay'];
        $paymentGateways = [
            'credit_card' => ['TaiXin Bank', 'Citi Bank', 'HSBC'],
            'line_pay' => ['LinePayGateway'],
            'bank_transfer' => ['TaiwanBank', 'CathayBank', 'ESunBank'],
            'ecpay' => ['ECPayGateway']
        ];
        
        // 生成最近30天的資料
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(10); // 只生成10天的資料
        
        // 記錄所有日期，用於生成對帳記錄
        $dates = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $dateStr;
            
            // 每天生成5-20筆交易
            $transactionsPerDay = rand(5, 20);
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // 選擇支付方式
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $paymentGateway = $paymentGateways[$paymentMethod][array_rand($paymentGateways[$paymentMethod])];
                
                // 隨機金額、手續費
                $amount = mt_rand(10000, 300000) / 100; // 100-3000元
                $fee = $amount * (mt_rand(1, 3) / 100); // 1-3%手續費
                $netAmount = $amount - $fee;
                
                // 隨機時間
                $hours = mt_rand(9, 21);
                $minutes = mt_rand(0, 59);
                $seconds = mt_rand(0, 59);
                $paymentDate = Carbon::parse($dateStr)->setTime($hours, $minutes, $seconds);
                
                // 隨機訂單ID和交易ID
                $orderId = 'ORD' . $date->format('Ymd') . strtoupper(Str::random(6));
                $transactionId = 'TRX' . strtoupper(Str::random(12));
                
                // 插入交易記錄
                DB::table('payment_transactions')->insert([
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'fee' => $fee,
                    'net_amount' => $netAmount,
                    'payment_method' => $paymentMethod,
                    'payment_gateway' => $paymentGateway,
                    'status' => 'completed',
                    'is_reconciled' => rand(0, 1) === 1, // 隨機對帳狀態
                    'payment_date' => $paymentDate,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate
                ]);
            }
        }
        
        // 為部分日期建立對帳記錄
        foreach ($dates as $index => $date) {
            // 只為2/3的日期建立對帳記錄
            if ($index % 3 !== 0) {
                // 查詢該日的所有交易
                $transactions = DB::table('payment_transactions')
                    ->whereDate('payment_date', $date)
                    ->get();
                
                $totalAmount = $transactions->sum('amount');
                $totalFee = $transactions->sum('fee');
                $totalNetAmount = $transactions->sum('net_amount');
                $count = $transactions->count();
                
                // 隨機對帳狀態
                $statuses = ['pending', 'matched', 'unmatched'];
                $status = $statuses[array_rand($statuses)];
                
                // 建立對帳記錄
                DB::table('payment_reconciliation')->insert([
                    'reconciliation_date' => $date,
                    'transaction_count' => $count,
                    'total_amount' => $totalAmount,
                    'total_fee' => $totalFee,
                    'total_net_amount' => $totalNetAmount,
                    'status' => $status,
                    'notes' => $status === 'unmatched' ? '與金流商對帳不符，需進一步確認' : ($status === 'matched' ? '已與金流商確認無誤' : null),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                
                // 更新交易的對帳狀態
                DB::table('payment_transactions')
                    ->whereDate('payment_date', $date)
                    ->update(['is_reconciled' => $status === 'matched']);
            }
        }
    }
}
