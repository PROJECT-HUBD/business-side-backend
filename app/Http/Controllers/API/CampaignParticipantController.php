<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignParticipantController extends Controller
{
    /**
     * 獲取指定活動的參與記錄
     */
    public function index($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $participants = $campaign->participants()->with('user')->latest()->get();
        
        return response()->json($participants);
    }
    
    /**
     * 記錄活動參與
     */
    public function store(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'order_id' => 'nullable|integer',
            'status' => 'nullable|in:pending,completed,cancelled',
            'discount_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // 檢查活動是否可用
        if (!$campaign->isAvailable()) {
            return response()->json(['error' => '活動不可用'], 400);
        }
        
        // 檢查庫存限制
        if ($campaign->stock_limit && $campaign->redemption_count >= $campaign->stock_limit) {
            return response()->json(['error' => '已達活動參與人數上限'], 400);
        }
        
        // 檢查每用戶限制
        if ($campaign->per_user_limit && $request->user_id) {
            $userParticipationCount = $campaign->participants()
                ->where('user_id', $request->user_id)
                ->where('status', 'completed')
                ->count();
                
            if ($userParticipationCount >= $campaign->per_user_limit) {
                return response()->json(['error' => '已達每位用戶參與次數上限'], 400);
            }
        }
        
        // 創建參與記錄
        $participant = new CampaignParticipant([
            'campaign_id' => $campaignId,
            'user_id' => $request->user_id,
            'order_id' => $request->order_id,
            'status' => $request->status ?? 'pending',
            'discount_amount' => $request->discount_amount,
            'joined_at' => Carbon::now(),
            'note' => $request->note,
        ]);
        
        $participant->save();
        
        // 如果狀態為完成，則更新活動參與次數
        if ($participant->status === 'completed') {
            $campaign->increment('redemption_count');
        }
        
        return response()->json($participant, 201);
    }
    
    /**
     * 更新參與狀態
     */
    public function updateStatus(Request $request, $campaignId, $participantId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $participant = CampaignParticipant::where('campaign_id', $campaignId)
            ->where('id', $participantId)
            ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,cancelled',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $oldStatus = $participant->status;
        $newStatus = $request->status;
        
        $participant->status = $newStatus;
        $participant->save();
        
        // 更新活動參與統計
        if ($oldStatus !== 'completed' && $newStatus === 'completed') {
            // 從非完成狀態變更為完成狀態，增加計數
            $campaign->increment('redemption_count');
        } else if ($oldStatus === 'completed' && $newStatus !== 'completed') {
            // 從完成狀態變更為其他狀態，減少計數
            if ($campaign->redemption_count > 0) {
                $campaign->decrement('redemption_count');
            }
        }
        
        return response()->json($participant);
    }
    
    /**
     * 刪除參與記錄
     */
    public function destroy($campaignId, $participantId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $participant = CampaignParticipant::where('campaign_id', $campaignId)
            ->where('id', $participantId)
            ->firstOrFail();
        
        // 如果狀態為完成，刪除前減少計數
        if ($participant->status === 'completed' && $campaign->redemption_count > 0) {
            $campaign->decrement('redemption_count');
        }
        
        $participant->delete();
        
        return response()->json(['message' => '參與記錄已刪除']);
    }
    
    /**
     * 獲取活動參與統計
     */
    public function getStats($campaignId)
    {
        // 暫時關閉 ONLY_FULL_GROUP_BY 限制
        DB::statement("SET SQL_MODE=''");
        
        $campaign = Campaign::findOrFail($campaignId);
        
        $totalParticipants = $campaign->redemption_count;
        $totalAmount = $campaign->participants()->where('status', 'completed')->sum('discount_amount');
        
        $statusCounts = $campaign->participants()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        
        $dailyStats = $campaign->participants()
            ->where('status', 'completed')
            ->selectRaw('DATE(joined_at) as date, COUNT(*) as count, SUM(discount_amount) as amount')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        
        // 恢復正常的 SQL_MODE
        DB::statement("SET SQL_MODE=(SELECT @@sql_mode)");
        
        return response()->json([
            'total_participants' => $totalParticipants,
            'total_discount_amount' => $totalAmount,
            'status_counts' => $statusCounts,
            'daily_stats' => $dailyStats
        ]);
    }
} 