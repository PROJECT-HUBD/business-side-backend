<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentTransaction;
use App\Models\PaymentReconciliation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentReconciliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空對帳表
        DB::table('payment_reconciliation')->truncate();

        // 獲取已有的交易記錄，按日期分組
        $transactionsByDate = PaymentTransaction::selectRaw('DATE(payment_date) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('date');
            
        // 隨機選擇一些日期進行對帳 (約50%)
        $datesToReconcile = $transactionsByDate->random(intval($transactionsByDate->count() / 2));
        
        foreach ($datesToReconcile as $date) {
            // 獲取該日期的所有交易
            $transactions = PaymentTransaction::whereDate('payment_date', $date)->get();
            
            // 創建對帳記錄
            PaymentReconciliation::create([
                'reconciliation_date' => $date,
                'transaction_count' => $transactions->count(),
                'total_amount' => $transactions->sum('amount'),
                'total_fee' => $transactions->sum('fee'),
                'total_net_amount' => $transactions->sum('net_amount'),
                'status' => 'matched',
                'notes' => '系統自動對帳 - ' . Carbon::now()->toDateTimeString(),
            ]);
            
            // 將對應的交易標記為已對帳
            foreach ($transactions as $transaction) {
                $transaction->is_reconciled = true;
                $transaction->save();
            }
        }
    }
}
