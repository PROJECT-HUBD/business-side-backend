<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CampaignController extends Controller
{
    /**
     * 獲取所有行銷活動
     */
    public function index()
    {
        $campaigns = Campaign::all();
        
        // 添加計算狀態到每個活動
        $campaigns->transform(function ($campaign) {
            $campaign->calculated_status = $campaign->calculated_status;
            return $campaign;
        });
        
        return response()->json($campaigns);
    }

    /**
     * 建立新行銷活動
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount,buy_x_get_y,bundle,flash_sale,free_shipping',
            'discount_method' => 'required_if:type,discount|nullable|in:percentage,fixed',
            'discount_value' => 'required_if:type,discount|nullable|numeric',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer',
            'free_quantity' => 'required_if:type,buy_x_get_y|nullable|integer',
            'bundle_quantity' => 'required_if:type,bundle|nullable|integer',
            'bundle_discount' => 'required_if:type,bundle|nullable|numeric',
            'flash_sale_start_time' => 'required_if:type,flash_sale|nullable|date',
            'flash_sale_end_time' => 'required_if:type,flash_sale|nullable|date|after:flash_sale_start_time',
            'flash_sale_discount' => 'required_if:type,flash_sale|nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'stock_limit' => 'nullable|integer',
            'per_user_limit' => 'nullable|integer',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'users' => 'nullable|array',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,disabled',
            'can_combine' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 處理日期格式與其他資料整理
        $data = $request->all();
        
        // 確保 users 欄位為陣列
        if (isset($data['users']) && !is_array($data['users'])) {
            $data['users'] = [];
        }
        
        // 確保狀態欄位有值
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }
        
        // 如果結束日期在過去，狀態應自動設為 disabled
        if (!empty($data['end_date']) && Carbon::parse($data['end_date'])->isPast() && $data['status'] === 'active') {
            $data['status'] = 'disabled';
        }

        $campaign = Campaign::create($data);
        $campaign->calculated_status = $campaign->calculated_status;
        
        return response()->json($campaign, 201);
    }

    /**
     * 更新行銷活動
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:discount,buy_x_get_y,bundle,flash_sale,free_shipping',
            'discount_method' => 'required_if:type,discount|nullable|in:percentage,fixed',
            'discount_value' => 'required_if:type,discount|nullable|numeric',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer',
            'free_quantity' => 'required_if:type,buy_x_get_y|nullable|integer',
            'bundle_quantity' => 'required_if:type,bundle|nullable|integer',
            'bundle_discount' => 'required_if:type,bundle|nullable|numeric',
            'flash_sale_start_time' => 'required_if:type,flash_sale|nullable|date',
            'flash_sale_end_time' => 'required_if:type,flash_sale|nullable|date|after:flash_sale_start_time',
            'flash_sale_discount' => 'required_if:type,flash_sale|nullable|numeric',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'stock_limit' => 'nullable|integer',
            'per_user_limit' => 'nullable|integer',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'users' => 'nullable|array',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,disabled',
            'can_combine' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 處理日期格式與其他資料整理
        $data = $request->all();
        
        // 確保 users 欄位為陣列
        if (isset($data['users']) && !is_array($data['users'])) {
            $data['users'] = [];
        }
        
        // 如果結束日期在過去，狀態應自動設為 disabled
        if (!empty($data['end_date']) && Carbon::parse($data['end_date'])->isPast() && isset($data['status']) && $data['status'] === 'active') {
            $data['status'] = 'disabled';
        }

        $campaign->update($data);
        $campaign->calculated_status = $campaign->calculated_status;
        
        return response()->json($campaign);
    }
}
