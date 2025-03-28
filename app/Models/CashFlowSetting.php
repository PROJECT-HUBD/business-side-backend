<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlowSetting extends Model
{
    use HasFactory;
    
    /**
     * 資料表名稱
     */
    protected $table = 'cash_flow_settings';
    
    /**
     * 主鍵
     */
    protected $primaryKey = 'name';
    
    /**
     * 設定主鍵是否為自增值
     */
    public $incrementing = false;
    
    /**
     * 主鍵型態
     */
    protected $keyType = 'string';
    
    /**
     * 可批量賦值的屬性
     */
    protected $fillable = [
        'name',
        'Hash_Key',
        'Hash_IV',
        'merchant_ID',
        'WEB_enable',
        'CVS_enable',
        'ATM_enable',
        'credit_enable',
    ];
    
    /**
     * 類型轉換
     */
    protected $casts = [
        'WEB_enable' => 'boolean',
        'CVS_enable' => 'boolean',
        'ATM_enable' => 'boolean',
        'credit_enable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * 獲取預設支付設定 (若有多個設定，預設取 ECPAY)
     */
    public static function getDefault()
    {
        return self::where('name', 'ECPAY')->first() ?? self::first();
    }
}
