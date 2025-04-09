<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('reconciliation_number')->unique()->comment('對帳編號');
            $table->date('reconciliation_date')->comment('對帳日期');
            $table->integer('transaction_count')->default(0)->comment('交易筆數');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('總金額');
            $table->unsignedBigInteger('staff_id')->nullable()->comment('對帳人員ID');
            $table->string('staff_name')->nullable()->comment('對帳人員姓名');
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();
            
            // 索引
            $table->index('reconciliation_date');
            $table->index('staff_id');
        });
        
        // 添加對帳相關欄位到 order_main 表
        Schema::table('order_main', function (Blueprint $table) {
            if (!Schema::hasColumn('order_main', 'reconciliation_status')) {
                $table->string('reconciliation_status')->nullable()->comment('對帳狀態: pending, completed, mismatch');
            }
            if (!Schema::hasColumn('order_main', 'reconciliation_notes')) {
                $table->text('reconciliation_notes')->nullable()->comment('對帳備註');
            }
            if (!Schema::hasColumn('order_main', 'reconciliation_date')) {
                $table->timestamp('reconciliation_date')->nullable()->comment('對帳時間');
            }
            if (!Schema::hasColumn('order_main', 'fee_amount')) {
                $table->decimal('fee_amount', 10, 2)->default(0)->comment('手續費');
            }
            if (!Schema::hasColumn('order_main', 'notes')) {
                $table->text('notes')->nullable()->comment('交易備註');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
        
        // 移除對帳相關欄位
        Schema::table('order_main', function (Blueprint $table) {
            $table->dropColumn([
                'reconciliation_status',
                'reconciliation_notes',
                'reconciliation_date',
                'fee_amount',
                'notes'
            ]);
        });
    }
};
