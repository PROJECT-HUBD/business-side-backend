<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'discount_type',
        'discount_value',
        'min_purchase',
        'start_date',
        'end_date',
        'usage_limit',
        'description',
        'products',
        'categories',
        'users',
        'buy_quantity',
        'free_quantity',
        'status',
        'can_combine',
    ];

    protected $casts = [
        'products' => 'array',
        'categories' => 'array',
        'users' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'usage_limit' => 'integer',
        'buy_quantity' => 'integer',
        'free_quantity' => 'integer',
        'can_combine' => 'boolean',
    ];

    protected $appends = ['calculated_status'];

    /**
     * 獲取優惠券的計算狀態
     * 基於日期範圍和手動設置的狀態計算實際狀態
     */
    public function getCalculatedStatusAttribute()
    {
        // 如果已手動設置為停用
        if ($this->status === 'disabled') {
            return 'disabled';
        }

        $now = Carbon::now();
        $startDate = $this->start_date ? Carbon::parse($this->start_date) : null;
        $endDate = $this->end_date ? Carbon::parse($this->end_date) : null;

        // 檢查是否已過期
        if ($endDate && $now->greaterThan($endDate)) {
            return 'expired';
        }
        
        // 檢查是否尚未開始
        if ($startDate && $now->lessThan($startDate)) {
            return 'scheduled';
        }
        
        // 其他情況為啟用狀態
        return 'active';
    }
    
    /**
     * 關聯到使用記錄
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
    
    /**
     * 檢查優惠券是否可用
     */
    public function isAvailable()
    {
        // 檢查狀態
        if ($this->calculated_status !== 'active') {
            return false;
        }
        
        // 檢查使用限制
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }
}
