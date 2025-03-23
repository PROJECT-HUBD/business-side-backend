<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * 獲取所有會員列表
     */
    public function index()
    {
        // 從 users 表格獲取會員列表
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->get();
        
        return response()->json($users);
    }
    
    /**
     * 獲取特定會員
     */
    public function show($id)
    {
        $user = DB::table('users')
            ->select('id', 'name', 'email', 'created_at')
            ->where('id', $id)
            ->first();
        
        if (!$user) {
            return response()->json(['message' => '會員不存在'], 404);
        }
        
        return response()->json($user);
    }
} 