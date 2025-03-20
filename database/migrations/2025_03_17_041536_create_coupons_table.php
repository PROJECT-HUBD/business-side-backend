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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->enum('discount_type', ['percentage', 'fixed', 'shipping', 'buy_x_get_y']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('min_purchase', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->text('description')->nullable();
            $table->json('products')->nullable();
            $table->json('categories')->nullable();
            $table->json('users')->nullable();
            $table->integer('buy_quantity')->nullable();
            $table->integer('free_quantity')->nullable();
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->boolean('can_combine')->default(false);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
