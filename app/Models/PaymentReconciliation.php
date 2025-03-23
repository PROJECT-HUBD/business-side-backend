<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    use HasFactory;

    protected $table = 'payment_reconciliation';

    protected $fillable = [
        'reconciliation_date',
        'transaction_count',
        'total_amount',
        'total_fee',
        'total_net_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
    ];

    /**
     * 獲取該日期的所有交易
     */
    public function transactions()
    {
        return $this->hasMany(
            PaymentTransaction::class,
            'payment_date',
            'reconciliation_date'
        )->whereDate('payment_date', $this->reconciliation_date);
    }
}
