<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductClassification;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 獲取所有產品分類
     */
    public function index()
    {
        $classifications = ProductClassification::all();
        
        // 將分類資料進行轉換，以便前端能夠方便使用
        $transformedClassifications = $classifications->map(function ($classification) {
            return [
                'id' => $classification->category_id,
                'name' => $classification->parent_category . ' - ' . $classification->child_category,
                'parent_category' => $classification->parent_category,
                'child_category' => $classification->child_category
            ];
        });
        
        return response()->json($transformedClassifications);
    }
    
    /**
     * 獲取特定分類
     */
    public function show($id)
    {
        $classification = ProductClassification::where('category_id', $id)->first();
        
        if (!$classification) {
            return response()->json(['message' => '分類不存在'], 404);
        }
        
        return response()->json([
            'id' => $classification->category_id,
            'name' => $classification->parent_category . ' - ' . $classification->child_category,
            'parent_category' => $classification->parent_category,
            'child_category' => $classification->child_category
        ]);
    }
} 