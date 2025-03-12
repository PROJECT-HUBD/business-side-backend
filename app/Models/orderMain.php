<?php

// OrderMain.php (Model)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMain extends Model
{
    use HasFactory;

    protected $table = 'order_main'; // 指定資料表名稱
    protected $primaryKey = 'order_id'; // 指定主鍵
    public $incrementing = false; // 如果 `order_id` 不是自增 ID
    protected $keyType = 'string'; // 如果 `order_id` 是字串類型

    // 建立與 order_detail 的關聯
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id'); 
    }
}
