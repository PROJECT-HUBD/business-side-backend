<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CouponController extends Controller
{
    /**
     * 獲取所有優惠券
     */
    public function index()
    {
        $coupons = Coupon::all();
        
        // 添加計算狀態到每個優惠券
        $coupons->transform(function ($coupon) {
            $coupon->calculated_status = $coupon->calculated_status;
            return $coupon;
        });
        
        return response()->json($coupons);
    }

    /**
     * 建立新優惠券
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons',
            'discount_type' => 'required|in:percentage,fixed,shipping,buy_x_get_y',
            'discount_value' => 'required_unless:discount_type,shipping,buy_x_get_y|nullable|numeric',
            'min_purchase' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer',
            'description' => 'nullable|string',
            'products' => 'nullable|array',
            'categories' => 'nullable|array',
            'users' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'buy_quantity' => 'required_if:discount_type,buy_x_get_y|nullable|integer',
            'free_quantity' => 'required_if:discount_type,buy_x_get_y|nullable|integer',
            'status' => 'nullable|in:active,disabled',
            'can_combine' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 處理日期格式
        $data = $request->all();
        
        // 確保 users 欄位為陣列
        if (isset($data['users']) && !is_array($data['users'])) {
            $data['users'] = [];
        }
        
        // 確保適用商品欄位為陣列
        if (isset($data['applicable_products']) && !is_array($data['applicable_products'])) {
            $data['applicable_products'] = [];
        }
        
        // 確保適用分類欄位為陣列
        if (isset($data['applicable_categories']) && !is_array($data['applicable_categories'])) {
            $data['applicable_categories'] = [];
        }
        
        // 確保狀態欄位有值
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }
        
        // 確保discount_value是精確值，而不是四捨五入的結果
        if (isset($data['discount_value']) && is_numeric($data['discount_value'])) {
            // 強制轉換為字串，確保不會有小數點精度問題
            $data['discount_value'] = (string)$data['discount_value'];
        }
        
        // 如果開始日期在未來，狀態為 active，但實際上是未來生效
        // 結束日期在過去，狀態應自動設為 disabled
        if (!empty($data['end_date']) && Carbon::parse($data['end_date'])->isPast() && $data['status'] === 'active') {
            $data['status'] = 'disabled';
        }

        $coupon = Coupon::create($data);
        $coupon->calculated_status = $coupon->calculated_status;
        
        return response()->json($coupon, 201);
    }

    /**
     * 更新優惠券
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50|unique:coupons,code,'.$id,
            'discount_type' => 'sometimes|required|in:percentage,fixed,shipping,buy_x_get_y',
            'discount_value' => 'sometimes|required_unless:discount_type,shipping,buy_x_get_y|nullable|numeric',
            'min_purchase' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer',
            'description' => 'nullable|string',
            'products' => 'nullable|array',
            'categories' => 'nullable|array',
            'users' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'buy_quantity' => 'required_if:discount_type,buy_x_get_y|nullable|integer',
            'free_quantity' => 'required_if:discount_type,buy_x_get_y|nullable|integer',
            'status' => 'nullable|in:active,disabled',
            'can_combine' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 處理日期格式
        $data = $request->all();
        
        // 確保 users 欄位為陣列
        if (isset($data['users']) && !is_array($data['users'])) {
            $data['users'] = [];
        }
        
        // 確保適用商品欄位為陣列
        if (isset($data['applicable_products']) && !is_array($data['applicable_products'])) {
            $data['applicable_products'] = [];
        }
        
        // 確保適用分類欄位為陣列
        if (isset($data['applicable_categories']) && !is_array($data['applicable_categories'])) {
            $data['applicable_categories'] = [];
        }
        
        // 確保狀態欄位有值
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }
        
        // 確保discount_value是精確值，而不是四捨五入的結果
        if (isset($data['discount_value']) && is_numeric($data['discount_value'])) {
            // 強制轉換為字串，確保不會有小數點精度問題
            $data['discount_value'] = (string)$data['discount_value'];
        }
        
        // 如果結束日期在過去，狀態應自動設為 disabled
        if (!empty($data['end_date']) && Carbon::parse($data['end_date'])->isPast() && isset($data['status']) && $data['status'] === 'active') {
            $data['status'] = 'disabled';
        }

        $coupon->update($data);
        $coupon->calculated_status = $coupon->calculated_status;
        
        return response()->json($coupon);
    }

    /**
     * 檢查優惠券代碼是否已存在
     */
    public function checkCode($code)
    {
        $exists = Coupon::where('code', $code)->exists();
        return response()->json(['exists' => $exists]);
    }
}
