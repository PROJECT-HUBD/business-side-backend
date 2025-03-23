<?php
// OrderController.php
namespace App\Http\Controllers;

use App\Models\OrderMain;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    // <---------------------------選全部的order+details------------------------------>

    public function getOrderWithDetails()
    {
        // 查詢所有的 order_main 並載入關聯的 order_details
        $orderMain = OrderMain::with('orderDetail') // 載入所有相關的 order_detail 資料
            ->get(); // 獲取所有資料

        // 檢查是否有資料
        if ($orderMain->isEmpty()) {
            return response()->json(['error' => 'No orders found'], 404);
        }

        return response()->json([
            'order_main' => $orderMain,             // 回傳所有 order_main 資料
            //  'order_detail' => $orderMain->pluck('orderDetail') // 回傳所有 order_details 資料
        ]);
    }
    // <---------------------------選全部order------------------------------>
    public  function getOrderAll()
    {
        return response()->json(OrderMain::all());
    }

    // <---------------------------選全部orderdetail------------------------------>
    public  function getOrderDetails()
    {
        return response()->json(OrderDetail::all());
    }


    // <---------------------------選單一筆orderdetails------------------------------>
    public function getOrder($order_id)
    {
        // 查詢該 order_id 的所有 order_detail 資料
        $orderDetails = OrderDetail::where('order_id', $order_id)->get();

        // 檢查是否找到資料
        if ($orderDetails->isEmpty()) {
            return response()->json(['error' => 'Order details not found for the given order_id'], 404);
        }

        // 回傳查詢結果
        return response()->json([
            // 'order_id' => $order_id,
            'order_details' => $orderDetails // 回傳該 order_id 的所有訂單詳細資料
        ]);
    }
}
