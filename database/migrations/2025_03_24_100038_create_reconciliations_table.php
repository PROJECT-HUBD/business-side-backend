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
            $table->decimal('total_amount', 10, 2)->default(0)->comment('交易總額');
            $table->decimal('total_fee', 10, 2)->nullable()->comment('手續費總額');
            $table->decimal('total_net_amount', 10, 2)->nullable()->comment('淨收入');
            $table->unsignedBigInteger('staff_id')->nullable()->comment('對帳人員ID');
            $table->string('staff_name')->nullable()->comment('對帳人員姓名');
            $table->enum('status', ['normal', 'abnormal', 'pending', 'completed'])->default('pending')->comment('對帳狀態');
            $table->text('notes')->nullable()->comment('對帳備註');
            $table->timestamps();
            
            // 索引
            $table->index('reconciliation_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};
