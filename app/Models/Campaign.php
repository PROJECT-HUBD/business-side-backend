<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'discount_method',
        'discount_value',
        'buy_quantity',
        'free_quantity',
        'bundle_quantity',
        'bundle_discount',
        'flash_sale_start_time',
        'flash_sale_end_time',
        'flash_sale_discount',
        'start_date',
        'end_date',
        'stock_limit',
        'per_user_limit',
        'applicable_products',
        'applicable_categories',
        'description',
        'status',
        'can_combine',
        'users',
    ];

    protected $casts = [
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'users' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'flash_sale_start_time' => 'datetime',
        'flash_sale_end_time' => 'datetime',
        'discount_value' => 'decimal:2',
        'bundle_discount' => 'decimal:2',
        'flash_sale_discount' => 'decimal:2',
        'stock_limit' => 'integer',
        'per_user_limit' => 'integer',
        'buy_quantity' => 'integer',
        'free_quantity' => 'integer',
        'bundle_quantity' => 'integer',
        'can_combine' => 'boolean',
    ];

    protected $appends = ['calculated_status'];

    /**
     * 獲取活動的計算狀態
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
     * 關聯到參與記錄
     */
    public function participants()
    {
        return $this->hasMany(CampaignParticipant::class);
    }
    
    /**
     * 檢查活動是否可用
     */
    public function isAvailable()
    {
        // 檢查狀態
        if ($this->calculated_status !== 'active') {
            return false;
        }
        
        // 檢查庫存限制
        if ($this->stock_limit && $this->redemption_count >= $this->stock_limit) {
            return false;
        }
        
        // 檢查限時特賣
        if ($this->type === 'flash_sale') {
            $now = Carbon::now();
            $flashStart = $this->flash_sale_start_time ? Carbon::parse($this->flash_sale_start_time) : null;
            $flashEnd = $this->flash_sale_end_time ? Carbon::parse($this->flash_sale_end_time) : null;
            
            if (!$flashStart || !$flashEnd || $now->lessThan($flashStart) || $now->greaterThan($flashEnd)) {
                return false;
            }
        }
        
        return true;
    }
}
