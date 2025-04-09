<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'fee',
        'net_amount',
        'payment_method',
        'payment_gateway',
        'status',
        'is_reconciled',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'is_reconciled' => 'boolean',
    ];

    /**
     * 獲取相關的訂單
     */
    public function order()
    {
        return $this->belongsTo(OrderMain::class, 'order_id', 'order_id');
    }

    /**
     * 獲取日期的支付對帳記錄
     */
    public function reconciliation()
    {
        return $this->hasOneThrough(
            PaymentReconciliation::class,
            PaymentTransaction::class,
            'id', // 本表的id
            'reconciliation_date', // reconciliation表中的日期
            'id', // 本表的id (local key)
            'payment_date' // 本表中的payment_date (second local key)
        )->whereDate('reconciliation_date', DB::raw('DATE(payment_date)'));
    }

    /**
     * 獲取該筆交易的淨收入
     */
    public function getNetAmountAttribute($value)
    {
        if (empty($value) && !empty($this->amount) && isset($this->fee)) {
            return $this->amount - $this->fee;
        }
        return $value;
    }
}
