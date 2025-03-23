<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentReconciliationTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_reconciliation', function (Blueprint $table) {
            $table->id();
            $table->date('reconciliation_date')->comment('對帳日期');
            $table->integer('transaction_count')->default(0)->comment('交易筆數');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('總金額');
            $table->decimal('total_fee', 10, 2)->default(0)->comment('總手續費');
            $table->decimal('total_net_amount', 12, 2)->default(0)->comment('總淨收入');
            $table->enum('status', ['pending', 'matched', 'unmatched'])->default('pending')->comment('對帳狀態');
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();

            // 添加唯一索引確保每日只有一筆對帳記錄
            $table->unique('reconciliation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliation');
    }
}
