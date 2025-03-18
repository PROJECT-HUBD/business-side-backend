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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['discount', 'buy_x_get_y', 'bundle', 'flash_sale', 'free_shipping']);
            $table->enum('discount_method', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->integer('buy_quantity')->nullable();
            $table->integer('free_quantity')->nullable();
            $table->integer('bundle_quantity')->nullable();
            $table->decimal('bundle_discount', 10, 2)->nullable();
            $table->timestamp('flash_sale_start_time')->nullable();
            $table->timestamp('flash_sale_end_time')->nullable();
            $table->decimal('flash_sale_discount', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('stock_limit')->nullable();
            $table->integer('per_user_limit')->nullable();
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->json('users')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->boolean('can_combine')->default(false);
            $table->integer('redemption_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
