<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // 獲取所有使用者，確保包含 birthday 欄位
            $users = User::all();
            
            // 調試日誌，檢查返回的數據
            \Log::info('Retrieved users data', ['count' => $users->count()]);
            \Log::info('First user data sample', ['first_user' => $users->first() ? $users->first()->toArray() : null]);
            
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error retrieving users', ['error' => $e->getMessage()]);
            return response()->json(['error' => '獲取用戶列表失敗'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'birthday' => 'nullable|date', // 確保生日是有效的日期格式
            ]);

            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            return response()->json($user, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating user', ['error' => $e->getMessage()]);
            return response()->json(['error' => '創建用戶失敗'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            \Log::error('Error retrieving user', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => '找不到指定用戶'], 404);
        }
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('Error deleting user', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => '刪除用戶失敗'], 500);
        }
    }
    
    /**
     * 添加隨機生日 - 測試用功能
     * 為用戶添加隨機生日，用於測試生日相關功能
     */
    public function addRandomBirthdays()
    {
        try {
            $users = DB::table('users')->get();
            $count = 0;
            
            foreach ($users as $user) {
                // 檢查用戶是否已有生日
                if (empty($user->birthday)) {
                    // 生成隨機年月日
                    $year = rand(1960, 2005);
                    $month = rand(1, 12);
                    $day = rand(1, 28); // 用28避免2月份問題
                    
                    // 格式化為YYYY-MM-DD
                    $birthday = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    
                    // 更新用戶生日
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['birthday' => $birthday]);
                    
                    $count++;
                }
            }
            
            return response()->json([
                'message' => "成功為 $count 位用戶添加隨機生日",
                'updated_count' => $count
            ]);
        } catch (\Exception $e) {
            \Log::error('Error adding random birthdays', ['error' => $e->getMessage()]);
            return response()->json(['error' => '添加隨機生日失敗: ' . $e->getMessage()], 500);
        }
    }
} 