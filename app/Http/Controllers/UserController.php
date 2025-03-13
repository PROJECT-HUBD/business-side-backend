<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
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
}
