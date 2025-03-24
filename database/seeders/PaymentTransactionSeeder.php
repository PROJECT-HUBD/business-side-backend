<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空表格
        DB::table('payment_transactions')->truncate();

        // 生成隨機訂單編號
        $orderIds = [];
        for ($i = 0; $i < 20; $i++) {
            $orderIds[] = 'ORD-' . strtoupper(Str::random(6));
        }

        // 支付方式
        $paymentMethods = ['credit_card', 'bank_transfer', 'third_party_payment'];
        $paymentGateways = ['stripe', 'paypal', 'newebpay', null];
        $statuses = ['completed', 'pending', 'failed', 'refunded'];

        // 生成過去30天的交易記錄
        for ($i = 0; $i < 50; $i++) {
            $randomDays = rand(0, 30);
            $amount = rand(500, 5000);
            $fee = $amount * 0.03; // 3% 手續費
            $netAmount = $amount - $fee;
            $isReconciled = (rand(0, 1) == 1);
            $status = $statuses[array_rand($statuses)];
            
            // 如果是退款，金額為負數
            if ($status === 'refunded') {
                $amount = -1 * $amount;
                $fee = -1 * $fee;
                $netAmount = -1 * $netAmount;
            }

            PaymentTransaction::create([
                'order_id' => $orderIds[array_rand($orderIds)],
                'transaction_id' => 'TXN-' . strtoupper(Str::random(8)),
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_gateway' => $paymentGateways[array_rand($paymentGateways)],
                'status' => $status,
                'is_reconciled' => $isReconciled,
                'payment_date' => Carbon::now()->subDays($randomDays),
            ]);
        }
    }
}
