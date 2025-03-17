<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    // 取得所有用戶資料
    public function index()
    {
        return response()->json(User::all());
    }

    // 取得特定用戶資料
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }


    public function getUserOrders($id)
{
    $totalOrders = DB::table('order_main')
        ->whereNotNull('id')//id不是null的
        ->where('id', $id)//根據id查詢
        ->count();//計算訂單數

    $totalSpent = DB::table('order_main')
        ->whereNotNull('id')//id不是nill的
        ->where('id', $id)
        ->sum('total_price_with_discount'); // 總消費金額

    return response()->json([
        'totalOrders' => $totalOrders,
        'totalSpent' => (float)$totalSpent
    ]);
}
}
