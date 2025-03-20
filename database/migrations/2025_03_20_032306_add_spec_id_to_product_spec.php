<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_spec', function (Blueprint $table) {
            // 檢查表中是否已經存在spec_id欄位
            if (!Schema::hasColumn('product_spec', 'spec_id')) {
                // 添加spec_id欄位
                $table->id('spec_id')->first();
            }
            
            // 如果表中有現有記錄且沒有主鍵，可以將現有記錄的ID更新為遞增值
            // 這會在遷移執行後由下面的代碼完成
        });
        
        // 給現有記錄設置遞增值
        DB::statement('SET @row_number = 0');
        DB::statement('UPDATE product_spec SET spec_id = (@row_number:=@row_number+1) WHERE spec_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_spec', function (Blueprint $table) {
            // 如果需要，可以移除spec_id欄位
            $table->dropColumn('spec_id');
        });
    }
};
