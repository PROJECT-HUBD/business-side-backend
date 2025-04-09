<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
        ->where('trade_status', '交易成功') // 只計算交易成功的訂單
        ->count();//計算訂單數

    $totalSpent = DB::table('order_main')
        ->whereNotNull('id')//id不是nill的
        ->where('id', $id)
        ->where('trade_status', '交易成功') // 只計算交易成功的訂單
        ->sum('total_price_with_discount'); // 總消費金額

    return response()->json([
        'totalOrders' => $totalOrders,
        'totalSpent' => (float)$totalSpent
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'phone' => 'nullable|string|max:20',
                'birthday' => 'nullable|date', // 確保生日是有效的日期格式
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json($user);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating user', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => '更新用戶失敗'], 500);
        }
    }
}