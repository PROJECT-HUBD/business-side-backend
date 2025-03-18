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
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamp('used_at');
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            // 注意：這些外鍵約束是基於假設 users 和 orders 表存在，如果不存在請先創建
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
}; 