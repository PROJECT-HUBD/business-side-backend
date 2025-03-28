<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->index()->comment('訂單ID');
            $table->string('transaction_id')->unique()->comment('交易ID');
            $table->decimal('amount', 10, 2)->comment('交易金額');
            $table->decimal('fee', 10, 2)->default(0)->comment('手續費');
            $table->decimal('net_amount', 10, 2)->comment('淨收入');
            $table->string('payment_method')->comment('支付方式');
            $table->string('payment_gateway')->nullable()->comment('支付閘道');
            $table->string('status')->default('completed')->comment('交易狀態');
            $table->boolean('is_reconciled')->default(false)->comment('是否已對帳');
            $table->timestamp('payment_date')->comment('支付日期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
}
