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
        // 獲取原始日期字符串
        $rawStartDate = $request->get('start_date');
        $rawEndDate = $request->get('end_date');
        
        // 解析日期並設置為當日開始/結束時間
        $startDate = $rawStartDate ? Carbon::parse($rawStartDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $rawEndDate ? Carbon::parse($rawEndDate)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
        
        // 調試日誌
        \Log::info('Dashboard API Request', [
            'raw_start_date' => $rawStartDate,
            'raw_end_date' => $rawEndDate,
            'parsed_start_date' => $startDate->toDateTimeString(),
            'parsed_end_date' => $endDate->toDateTimeString(),
        ]);
        
        // 篩選支付方式
        $paymentMethod = $request->get('payment_method');
        
        // 基本查詢構建 - 使用 order_main 表而不是 payment_transactions
        $query = DB::table('order_main')
            ->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()]);
        
        if ($paymentMethod) {
            $query->where('payment_type', $paymentMethod);
        }
        
        // 統計資料
        $stats = [
            'total_sales' => $query->sum('total_price_with_discount'),
            'transaction_count' => $query->count(),
            'total_fees' => $query->sum('fee_amount'),
            'net_income' => $query->sum(DB::raw('total_price_with_discount - IFNULL(fee_amount, 0)')),
        ];
        
        // 計算待對帳和已對帳的天數
        $allDatesWithTransactions = DB::table('order_main')
            ->select(DB::raw('DISTINCT DATE(trade_Date) as date'))
            ->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->orderBy('date')
            ->pluck('date')
            ->toArray();
            
        // 已對帳的日期 (對帳狀態為 normal, abnormal 或 completed)
        $reconciledDates = DB::table('order_main')
            ->select(DB::raw('DISTINCT DATE(trade_Date) as date'))
            ->whereIn('reconciliation_status', ['normal', 'abnormal', 'completed'])
            ->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->pluck('date')
            ->toArray();
            
        // 待對帳天數 = 有交易的天數 - 已對帳的天數
        $stats['pending_reconciliation'] = count(array_diff($allDatesWithTransactions, $reconciledDates));
        $stats['completed_reconciliation'] = count($reconciledDates);
        
        // 依日期分組的交易數據 (用於圖表)
        $dailyStats = DB::table('order_main')
            ->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->when($paymentMethod, function ($q) use ($paymentMethod) {
                return $q->where('payment_type', $paymentMethod);
            })
            ->select(
                DB::raw('DATE(trade_Date) as date'),
                DB::raw('SUM(total_price_with_discount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(fee_amount) as total_fee'),
                DB::raw('SUM(total_price_with_discount - IFNULL(fee_amount, 0)) as total_net_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 支付方式分佈
        $paymentMethods = DB::table('order_main')
            ->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->select('payment_type as payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_price_with_discount) as total_amount'))
            ->groupBy('payment_type')
            ->orderByDesc('total_amount')
            ->get();
        
        // 調試日誌
        \Log::info('Dashboard API Response', [
            'stats' => $stats,
            'daily_stats_count' => $dailyStats->count(),
            'date_range' => $dailyStats->pluck('date')->toArray()
        ]);
        
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
        
        // 直接返回結果，不分組
        $transactions = $query->latest('payment_date')->get();
        
        return response()->json($transactions);
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
    
    /**
     * 獲取金流統計數據 (用於前端)
     */
    public function getStats()
    {
        // 總收入 (已完成交易)
        $totalIncome = PaymentTransaction::where('status', 'completed')
            ->sum('amount');
            
        // 總支出 (退款等)
        $totalOutcome = PaymentTransaction::where('status', 'refunded')
            ->sum('amount');
            
        // 待對帳交易數量
        $pendingReconciliation = PaymentTransaction::where('status', 'completed')
            ->where('is_reconciled', false)
            ->count();
            
        // 已對帳交易數量
        $completedReconciliation = PaymentTransaction::where('status', 'completed')
            ->where('is_reconciled', true)
            ->count();
            
        return response()->json([
            'totalIncome' => $totalIncome,
            'totalOutcome' => $totalOutcome,
            'pendingReconciliation' => $pendingReconciliation,
            'completedReconciliation' => $completedReconciliation
        ]);
    }
    
    /**
     * 獲取對帳記錄列表
     */
    public function getReconciliations()
    {
        $reconciliations = PaymentReconciliation::orderBy('reconciliation_date', 'desc')
            ->get()
            ->map(function ($item) {
                // 生成對帳編號
                $reconciliationNumber = 'R-' . str_replace('-', '', $item->reconciliation_date) . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT);
                
                return [
                    'id' => $item->id,
                    'reconciliation_number' => $reconciliationNumber,
                    'reconciliation_date' => $item->reconciliation_date,
                    'transaction_count' => $item->transaction_count,
                    'total_amount' => $item->total_amount,
                    'staff_name' => $item->staff_name ?? 'System',
                    'status' => $item->status,
                    'notes' => $item->notes,
                    'created_at' => $item->created_at
                ];
            });
            
        return response()->json($reconciliations);
    }
    
    /**
     * 對帳單筆交易
     */
    public function reconcileTransaction($id)
    {
        $transaction = PaymentTransaction::findOrFail($id);
        
        if ($transaction->status !== 'completed') {
            return response()->json([
                'message' => '只有已完成的交易可以進行對帳'
            ], 400);
        }
        
        // 更新交易對帳狀態
        $transaction->is_reconciled = true;
        $transaction->save();
        
        // 取得或創建對帳記錄
        $date = $transaction->payment_date->toDateString();
        $reconciliation = PaymentReconciliation::firstOrNew(['reconciliation_date' => $date]);
        
        // 如果是新記錄，計算統計資料
        if (!$reconciliation->exists) {
            $dayTransactions = PaymentTransaction::whereDate('payment_date', $date)->get();
            $reconciliation->transaction_count = $dayTransactions->count();
            $reconciliation->total_amount = $dayTransactions->sum('amount');
            $reconciliation->total_fee = $dayTransactions->sum('fee');
            $reconciliation->total_net_amount = $dayTransactions->sum('net_amount');
            $reconciliation->status = 'completed';
        }
        
        // 添加新的交易對帳註記
        $notes = $reconciliation->notes ?? '';
        $reconciliation->notes = trim($notes . "\n" . "Transaction {$transaction->transaction_id} reconciled on " . now());
        $reconciliation->save();
        
        // 生成新的對帳記錄
        $reconciliationNumber = 'R-' . str_replace('-', '', $date) . '-' . rand(1000, 9999);
        
        return response()->json([
            'message' => '交易已成功對帳',
            'transaction' => [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_id,
                'reconciliation_status' => 'completed'
            ],
            'reconciliation' => [
                'reconciliation_number' => $reconciliationNumber,
                'reconciliation_date' => $date,
                'transaction_number' => $transaction->transaction_id
            ]
        ]);
    }

    /**
     * 獲取日交易摘要列表
     */
    public function getDailyTransactionsSummary(Request $request)
    {
        // 確保日期格式正確
        $rawStartDate = $request->input('start_date');
        $rawEndDate = $request->input('end_date');
        
        $startDate = $rawStartDate ? Carbon::parse($rawStartDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $rawEndDate ? Carbon::parse($rawEndDate)->endOfDay() : Carbon::now()->endOfDay();
        
        // 日誌記錄，調試用
        \Log::info('Daily Transaction Summary Request', [
            'raw_start_date' => $rawStartDate,
            'raw_end_date' => $rawEndDate,
            'parsed_start_date' => $startDate->toDateTimeString(),
            'parsed_end_date' => $endDate->toDateTimeString(),
        ]);
        
        $paymentMethod = $request->input('payment_method');
        $reconciliationStatus = $request->input('reconciliation_status');
        
        // 使用SQL_MODE='' 暫時關閉 ONLY_FULL_GROUP_BY 限制
        DB::statement("SET SQL_MODE=''");
        
        $query = DB::table('order_main')
            ->selectRaw('DATE(trade_Date) as date')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(total_price_with_discount) as total_amount')
            ->selectRaw('SUM(IFNULL(fee_amount, 0)) as total_fee')
            ->selectRaw('SUM(total_price_with_discount - IFNULL(fee_amount, 0)) as total_net_amount')
            ->selectRaw('MAX(CASE WHEN reconciliation_status = "completed" THEN "completed" ELSE "pending" END) as reconciliation_status')
            ->selectRaw('MAX(reconciliation_notes) as reconciliation_notes')
            ->selectRaw('EXISTS(SELECT 1 FROM order_main om WHERE DATE(om.trade_Date) = DATE(order_main.trade_Date) AND om.notes IS NOT NULL AND om.notes != "") as has_note')
            ->whereNotNull('trade_Date');
            
        // 使用確切的日期時間範圍篩選
        $query->whereBetween('trade_Date', [$startDate->toDateTimeString(), $endDate->toDateTimeString()]);
        
        if ($paymentMethod) {
            $query->where('payment_type', $paymentMethod);
        }
        
        if ($reconciliationStatus) {
            $query->where('reconciliation_status', $reconciliationStatus);
        }
        
        $dailyTransactions = $query->groupBy(DB::raw('DATE(trade_Date)'))
            ->orderBy('date', 'desc')
            ->get();
        
        // 記錄返回的記錄數量，調試用
        \Log::info('Daily Transaction Summary Response', [
            'record_count' => $dailyTransactions->count(),
            'date_range' => $dailyTransactions->pluck('date')->toArray()
        ]);
        
        // 恢復正常的 SQL_MODE
        DB::statement("SET SQL_MODE=(SELECT @@sql_mode)");
        
        return response()->json($dailyTransactions);
    }
    
    /**
     * 獲取指定日期的所有交易詳情 (新版 - 從 order_main 表)
     * 
     * @param Request $request
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyTransactionDetail(Request $request, $date)
    {
        // 驗證日期格式
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => '日期格式不正確，正確格式為 YYYY-MM-DD'], 400);
        }
        
        // 獲取該日所有交易
        $transactions = DB::table('order_main as om')
            ->select([
                'om.id',
                'om.order_id',
                'om.trade_No as transaction_id',
                'om.total_price_with_discount as amount',
                'om.trade_Date as payment_date',
                'om.payment_type as payment_method',
                'om.trade_status as status',
                'om.reconciliation_status',
                'om.notes'
            ])
            ->whereRaw('DATE(om.trade_Date) = ?', [$date])
            ->orderBy('om.trade_Date', 'desc')
            ->get();
        
        // 獲取該日交易統計
        $stats = DB::table('order_main')
            ->select([
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_price_with_discount) as total_amount'),
                DB::raw('SUM(fee_amount) as total_fee'),
                DB::raw('SUM(total_price_with_discount - fee_amount) as total_net_amount'),
                DB::raw('MAX(CASE WHEN reconciliation_status = "completed" THEN "completed" ELSE "pending" END) as reconciliation_status'),
                DB::raw('MAX(reconciliation_notes) as reconciliation_notes'),
            ])
            ->whereRaw('DATE(trade_Date) = ?', [$date])
            ->first();
        
        // 獲取訂單詳情 (可選，如果需要顯示訂單項目)
        foreach ($transactions as $transaction) {
            $orderDetails = DB::table('order_detail')
                ->where('order_id', $transaction->order_id)
                ->get();
                
            $transaction->order_items = $orderDetails;
        }
        
        return response()->json([
            'date' => $date,
            'stats' => $stats,
            'transactions' => $transactions
        ]);
    }

    /**
     * 對指定日期的交易進行對帳 (新版 - 使用 order_main 表)
     * 
     * @param Request $request
     * @param string $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function reconcileDailyTransactions(Request $request, $date)
    {
        // 驗證日期格式
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => '日期格式不正確，正確格式為 YYYY-MM-DD'], 400);
        }
        
        // 驗證狀態
        $status = $request->input('status', 'normal');
        if (!in_array($status, ['normal', 'abnormal', 'pending'])) {
            return response()->json(['error' => '狀態不正確，應為 normal, abnormal 或 pending'], 400);
        }
        
        $notes = $request->input('notes', '系統自動對帳');
        
        // 更新該日所有交易的對帳狀態
        $affectedRows = DB::table('order_main')
            ->whereRaw('DATE(trade_Date) = ?', [$date])
            ->update([
                'reconciliation_status' => $status,
                'reconciliation_notes' => $notes,
                'reconciliation_date' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        
        // 產生對帳紀錄
        if ($affectedRows > 0) {
            // 獲取交易統計
            $stats = DB::table('order_main')
                ->select([
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(total_price_with_discount) as total_amount'),
                ])
                ->whereRaw('DATE(trade_Date) = ?', [$date])
                ->first();
            
            // 插入對帳紀錄
            $reconciliationId = DB::table('reconciliations')->insertGetId([
                'reconciliation_number' => 'REC' . time(),
                'reconciliation_date' => $date,
                'transaction_count' => $stats->transaction_count,
                'total_amount' => $stats->total_amount,
                'staff_id' => $request->user() ? $request->user()->id : null,
                'staff_name' => $request->user() ? $request->user()->name : '系統',
                'status' => $status, // 儲存對帳狀態
                'notes' => $notes,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => '對帳成功',
            'affected_rows' => $affectedRows,
            'reconciliation_id' => $reconciliationId ?? null
        ]);
    }
    
    /**
     * 獲取對帳記錄列表 (新版 - 使用 reconciliations 表)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyReconciliations(Request $request)
    {
        // 解析日期並設置為當日開始/結束時間
        $rawStartDate = $request->input('start_date');
        $rawEndDate = $request->input('end_date');
        
        $startDate = $rawStartDate ? Carbon::parse($rawStartDate)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $rawEndDate ? Carbon::parse($rawEndDate)->endOfDay() : Carbon::now()->endOfDay();
        
        // 調試日誌
        \Log::info('Reconciliations API Request', [
            'raw_start_date' => $rawStartDate,
            'raw_end_date' => $rawEndDate,
            'parsed_start_date' => $startDate->toDateString(),
            'parsed_end_date' => $endDate->toDateString(),
        ]);
        
        $reconciliations = DB::table('reconciliations')
            ->whereBetween('reconciliation_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('reconciliation_date', 'desc')
            ->get();
            
        // 調試日誌
        \Log::info('Reconciliations API Response', [
            'record_count' => $reconciliations->count(),
            'dates' => $reconciliations->pluck('reconciliation_date')->toArray()
        ]);
        
        return response()->json($reconciliations);
    }

    /**
     * 為特定交易添加備註 (使用 order_main 表)
     * 
     * @param Request $request
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrderNote(Request $request, $transactionId)
    {
        $note = $request->input('note');
        
        if (!$note) {
            return response()->json(['error' => '備註內容不能為空'], 400);
        }
        
        $updated = DB::table('order_main')
            ->where('id', $transactionId)
            ->update([
                'notes' => $note,
                'updated_at' => Carbon::now()
            ]);
        
        if (!$updated) {
            return response()->json(['error' => '交易記錄不存在'], 404);
        }
        
        return response()->json(['success' => true, 'message' => '備註已添加']);
    }

    /**
     * 獲取金流統計資料 (新版 - 使用 order_main 表)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyTransactionStats(Request $request)
    {
        // 暫時關閉 ONLY_FULL_GROUP_BY 限制
        DB::statement("SET SQL_MODE=''");
        
        // 計算總收入 (正向交易)
        $totalIncome = DB::table('order_main')
            ->where('total_price_with_discount', '>', 0)
            ->sum('total_price_with_discount');
        
        // 計算總支出 (負向交易，如退款)
        $totalOutcome = DB::table('order_main')
            ->where('total_price_with_discount', '<', 0)
            ->sum('total_price_with_discount');
        
        // 計算未對帳的天數
        $pendingReconciliation = DB::table('order_main')
            ->select(DB::raw('DATE(trade_Date) as date'))
            ->whereNull('reconciliation_status')
            ->orWhere('reconciliation_status', '!=', 'completed')
            ->groupBy(DB::raw('DATE(trade_Date)'))
            ->count();
        
        // 計算已對帳的天數
        $completedReconciliation = DB::table('order_main')
            ->select(DB::raw('DATE(trade_Date) as date'))
            ->where('reconciliation_status', 'completed')
            ->groupBy(DB::raw('DATE(trade_Date)'))
            ->count();
        
        // 恢復正常的 SQL_MODE
        DB::statement("SET SQL_MODE=(SELECT @@sql_mode)");
        
        return response()->json([
            'totalIncome' => $totalIncome,
            'totalOutcome' => abs($totalOutcome),
            'pendingReconciliation' => $pendingReconciliation,
            'completedReconciliation' => $completedReconciliation
        ]);
    }

    /**
     * 獲取單筆訂單詳情
     * 
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderDetail($orderId)
    {
        // 獲取訂單主表資訊
        $order = DB::table('order_main')->where('order_id', $orderId)->first();
        
        if (!$order) {
            return response()->json(['error' => '找不到訂單'], 404);
        }
        
        // 獲取訂單詳細項目
        $orderItems = DB::table('order_detail')->where('order_id', $orderId)->get();
        
        // 組合訂單完整資訊
        $orderData = (array) $order;
        $orderData['order_items'] = $orderItems;
        
        return response()->json($orderData);
    }

    /**
     * 獲取金流圖表數據 (用於前端圖表展示)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method');
        
        // 暫時關閉 ONLY_FULL_GROUP_BY 限制
        DB::statement("SET SQL_MODE=''");
        
        $query = DB::table('order_main')
            ->selectRaw('DATE(trade_Date) as date')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(total_price_with_discount) as total_amount')
            ->selectRaw('SUM(IFNULL(fee_amount, 0)) as total_fee')
            ->selectRaw('SUM(total_price_with_discount - IFNULL(fee_amount, 0)) as total_net_amount')
            ->whereNotNull('trade_Date')
            ->whereBetween('trade_Date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($paymentMethod) {
            $query->where('payment_type', $paymentMethod);
        }
        
        $chartData = $query->groupBy(DB::raw('DATE(trade_Date)'))
            ->orderBy('date', 'asc')
            ->get();
            
        // 恢復正常的 SQL_MODE
        DB::statement("SET SQL_MODE=(SELECT @@sql_mode)");
        
        return response()->json($chartData);
    }
}
