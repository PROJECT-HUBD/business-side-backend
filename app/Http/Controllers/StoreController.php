<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Banners;

class StoreController extends Controller
{
    // 商品管理

    // 賣場管理
    // 取得banner
    public function index()
    {
        $banners = Banners::all();
        return response()->json($banners);
    }

    // 更新指定 banner
    public function update(Request $request, $id)
    {
        // 如果 `banner_img` 有值則驗證，否則允許它為 `nullable`
        $rules = [
            'banner_title' => 'required|string|min:3|max:15',
            'banner_description' => 'required|string|min:3|max:65',
            'banner_link' => 'required|string|max:255',
        ];

        // 只有當用戶上傳圖片時才驗證 `banner_img`
        if ($request->hasFile('banner_img')) {
            $rules['banner_img'] = 'image|mimes:jpeg,png,jpg,gif|max:5120';
        }

        // 驗證請求
        $request->validate($rules);

        // 找到 banner
        $banner = Banners::findOrFail($id);
        $banner->banner_title = $request->banner_title;
        $banner->banner_description = $request->banner_description;
        $banner->banner_link = $request->banner_link;

        // ✅ 只有當用戶有上傳新圖片時才更新圖片
        if ($request->hasFile('banner_img')) {
            $path = $request->file('banner_img')->store('banners', 'public');
            $banner->banner_img = $path;
        }

        $banner->save();
        return response()->json(['message' => 'Banner 更新成功！']);
    }
}
