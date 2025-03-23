<?php
// OrderDetail.php (Model)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_detail';
    public $incrementing = true;

    public function orderMain()
    {
        return $this->belongsTo(OrderMain::class, 'order_id', 'order_id'); 
    }
}
