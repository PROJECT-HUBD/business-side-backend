<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\PaymentReconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * 獲取金流管理儀表板的統計數據
     */
    public function dashboard(Request $request)
    {
        // 預設為當前月份
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfMonth();
        
        // 篩選支付方式
        $paymentMethod = $request->get('payment_method');
        
        // 基本查詢構建
        $query = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate]);
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        // 統計資料
        $stats = [
            'total_sales' => $query->sum('amount'),
            'transaction_count' => $query->count(),
            'total_fees' => $query->sum('fee'),
            'net_income' => $query->sum('net_amount'),
        ];
        
        // 依日期分組的交易數據 (用於圖表)
        $dailyStats = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate])
            ->when($paymentMethod, function ($q) use ($paymentMethod) {
                return $q->where('payment_method', $paymentMethod);
            })
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(fee) as total_fee'),
                DB::raw('SUM(net_amount) as total_net_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 支付方式分佈
        $paymentMethods = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
        
        return response()->json([
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'stats' => $stats,
            'daily_stats' => $dailyStats,
            'payment_methods' => $paymentMethods,
        ]);
    }
    
    /**
     * 獲取交易列表 (含分頁與篩選)
     */
    public function getTransactions(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $paymentMethod = $request->get('payment_method');
        $reconcileStatus = $request->get('reconcile_status'); // 'all', 'reconciled', 'unreconciled'
        
        $query = PaymentTransaction::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [Carbon::parse($startDate), Carbon::parse($endDate)]);
        }
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        if ($reconcileStatus) {
            if ($reconcileStatus === 'reconciled') {
                $query->where('is_reconciled', true);
            } elseif ($reconcileStatus === 'unreconciled') {
                $query->where('is_reconciled', false);
            }
        }
        
        // 按日期獲取分組的交易數據
        $groupedTransactions = $query->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(fee) as total_fee'),
                DB::raw('SUM(net_amount) as total_net_amount')
            )
            ->groupBy('date')
            ->orderByDesc('date');
        
        // 依據分組後的數據加入對帳狀態
        $data = $groupedTransactions->paginate($perPage);
        
        // 加入對帳狀態資訊
        foreach ($data as $item) {
            $date = $item->date;
            $reconciliation = PaymentReconciliation::where('reconciliation_date', $date)->first();
            $item->reconcile_status = $reconciliation ? $reconciliation->status : 'pending';
        }
        
        return response()->json($data);
    }
    
    /**
     * 獲取特定日期的交易詳細資料
     */
    public function getDailyTransactions(Request $request, $date)
    {
        $date = Carbon::parse($date);
        $paymentMethod = $request->get('payment_method');
        
        $query = PaymentTransaction::whereDate('payment_date', $date);
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        // 獲取該日交易明細
        $transactions = $query->get();
        
        // 獲取該日的對帳狀態
        $reconciliation = PaymentReconciliation::where('reconciliation_date', $date->toDateString())->first();
        
        // 計算統計數據
        $stats = [
            'date' => $date->toDateString(),
            'transaction_count' => $transactions->count(),
            'total_amount' => $transactions->sum('amount'),
            'total_fee' => $transactions->sum('fee'),
            'total_net_amount' => $transactions->sum('net_amount'),
            'reconcile_status' => $reconciliation ? $reconciliation->status : 'pending',
            'notes' => $reconciliation ? $reconciliation->notes : null,
        ];
        
        return response()->json([
            'stats' => $stats,
            'transactions' => $transactions,
        ]);
    }
    
    /**
     * 更新對帳狀態
     */
    public function updateReconciliation(Request $request, $date)
    {
        $request->validate([
            'status' => 'required|in:pending,matched,unmatched',
            'notes' => 'nullable|string',
        ]);
        
        $date = Carbon::parse($date)->toDateString();
        
        // 獲取或建立對帳記錄
        $reconciliation = PaymentReconciliation::firstOrNew(['reconciliation_date' => $date]);
        
        // 計算該日交易總數與金額
        $transactions = PaymentTransaction::whereDate('payment_date', $date)->get();
        $transactionCount = $transactions->count();
        $totalAmount = $transactions->sum('amount');
        $totalFee = $transactions->sum('fee');
        $totalNetAmount = $transactions->sum('net_amount');
        
        // 更新對帳資訊
        $reconciliation->transaction_count = $transactionCount;
        $reconciliation->total_amount = $totalAmount;
        $reconciliation->total_fee = $totalFee;
        $reconciliation->total_net_amount = $totalNetAmount;
        $reconciliation->status = $request->status;
        $reconciliation->notes = $request->notes;
        $reconciliation->save();
        
        // 更新相關交易的對帳狀態，但不更新payment_date欄位
        $isReconciled = ($request->status === 'matched');
        
        // 獲取該日期的交易 IDs
        $transactionIds = PaymentTransaction::whereDate('payment_date', $date)->pluck('id')->toArray();
        
        // 使用原始SQL更新，不更新timestamps
        DB::statement("UPDATE payment_transactions SET is_reconciled = ? WHERE id IN (" . implode(',', $transactionIds) . ")", [
            $isReconciled ? 1 : 0
        ]);
        
        return response()->json([
            'message' => '對帳狀態已更新',
            'reconciliation' => $reconciliation,
        ]);
    }
    
    /**
     * 匯出交易資料 (CSV格式)
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfMonth();
        $paymentMethod = $request->get('payment_method');
        
        $query = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate]);
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        $transactions = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv"',
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // 添加UTF-8 BOM
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV 標題
            fputcsv($file, [
                '交易日期',
                '交易ID',
                '訂單ID',
                '支付方式',
                '支付閘道',
                '金額',
                '手續費',
                '淨收入',
                '交易狀態',
                '對帳狀態',
            ]);
            
            // 寫入資料
            foreach ($transactions as $item) {
                fputcsv($file, [
                    $item->payment_date,
                    $item->transaction_id,
                    $item->order_id,
                    $item->payment_method,
                    $item->payment_gateway ?? 'N/A',
                    $item->amount,
                    $item->fee,
                    $item->net_amount,
                    $item->status,
                    $item->is_reconciled ? '已對帳' : '未對帳',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * 匯出交易資料 (Excel格式)
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfMonth();
        $paymentMethod = $request->get('payment_method');
        
        $query = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate]);
        
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }
        
        $transactions = $query->get();
        
        // 將結果轉換為可匯出到Excel的格式
        $exportData = [];
        foreach ($transactions as $item) {
            $exportData[] = [
                '交易日期' => $item->payment_date,
                '交易ID' => $item->transaction_id,
                '訂單ID' => $item->order_id,
                '支付方式' => $item->payment_method,
                '支付閘道' => $item->payment_gateway ?? 'N/A',
                '金額' => $item->amount,
                '手續費' => $item->fee,
                '淨收入' => $item->net_amount,
                '交易狀態' => $item->status,
                '對帳狀態' => $item->is_reconciled ? '已對帳' : '未對帳',
            ];
        }
        
        $filename = 'transactions_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        
        // 實際實現將由前端處理，這裡僅返回數據
        return response()->json([
            'data' => $exportData,
            'filename' => $filename,
        ]);
    }
}
