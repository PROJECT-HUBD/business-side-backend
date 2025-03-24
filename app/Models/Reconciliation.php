<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    use HasFactory;
    
    /**
     * 資料表名稱
     */
    protected $table = 'reconciliations';
    
    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'reconciliation_number',
        'reconciliation_date',
        'transaction_count',
        'total_amount',
        'total_fee',
        'total_net_amount',
        'staff_id',
        'staff_name',
        'status',
        'notes',
    ];
    
    /**
     * 類型轉換
     */
    protected $casts = [
        'reconciliation_date' => 'date',
        'transaction_count' => 'integer',
        'total_amount' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * 生成唯一的對帳編號
     */
    public static function generateReconciliationNumber()
    {
        $date = date('Ymd');
        $random = mt_rand(1000, 9999);
        return 'REC' . $date . $random;
    }
}
