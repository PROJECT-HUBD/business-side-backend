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
        Schema::table('order_main', function (Blueprint $table) {
            // 增加與金流相關的欄位
            $table->decimal('fee_amount', 10, 2)->nullable()->comment('手續費金額');
            $table->string('reconciliation_status')->nullable()->comment('對帳狀態: pending, normal, abnormal, completed');
            $table->text('reconciliation_notes')->nullable()->comment('對帳備註');
            $table->timestamp('reconciliation_date')->nullable()->comment('對帳日期');
            $table->text('notes')->nullable()->comment('交易備註');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_main', function (Blueprint $table) {
            $table->dropColumn([
                'fee_amount',
                'reconciliation_status',
                'reconciliation_notes',
                'reconciliation_date',
                'notes'
            ]);
        });
    }
};
