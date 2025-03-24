<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CashFlowSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashFlowController extends Controller
{
    /**
     * 獲取所有金流設定
     */
    public function index()
    {
        $settings = CashFlowSetting::all();
        return response()->json($settings);
    }

    /**
     * 獲取單一金流設定
     */
    public function show($name)
    {
        $setting = CashFlowSetting::find($name);
        
        if (!$setting) {
            return response()->json([
                'message' => '找不到金流設定'
            ], 404);
        }
        
        return response()->json($setting);
    }

    /**
     * 建立新的金流設定
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:cash_flow_settings,name',
            'Hash_Key' => 'required|string|max:50',
            'Hash_IV' => 'required|string|max:50',
            'merchant_ID' => 'required|string|max:50',
            'WEB_enable' => 'boolean',
            'CVS_enable' => 'boolean',
            'ATM_enable' => 'boolean',
            'credit_enable' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $setting = CashFlowSetting::create($request->all());
        
        return response()->json([
            'message' => '金流設定建立成功',
            'data' => $setting
        ], 201);
    }

    /**
     * 更新金流設定
     */
    public function update(Request $request, $name)
    {
        $setting = CashFlowSetting::find($name);
        
        if (!$setting) {
            return response()->json([
                'message' => '找不到金流設定'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'Hash_Key' => 'string|max:50',
            'Hash_IV' => 'string|max:50',
            'merchant_ID' => 'string|max:50',
            'WEB_enable' => 'boolean',
            'CVS_enable' => 'boolean',
            'ATM_enable' => 'boolean',
            'credit_enable' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => '驗證失敗',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $setting->update($request->all());
        
        return response()->json([
            'message' => '金流設定更新成功',
            'data' => $setting
        ]);
    }

    /**
     * 刪除金流設定
     */
    public function destroy($name)
    {
        $setting = CashFlowSetting::find($name);
        
        if (!$setting) {
            return response()->json([
                'message' => '找不到金流設定'
            ], 404);
        }
        
        $setting->delete();
        
        return response()->json([
            'message' => '金流設定刪除成功'
        ]);
    }
}
