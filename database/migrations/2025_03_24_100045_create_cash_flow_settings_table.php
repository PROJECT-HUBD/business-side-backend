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
        // 已經存在於資料庫中，此處可不執行建立
        if (!Schema::hasTable('cash_flow_settings')) {
            Schema::create('cash_flow_settings', function (Blueprint $table) {
                $table->string('name', 50)->primary();
                $table->string('Hash_Key', 50);
                $table->string('Hash_IV', 50);
                $table->string('merchant_ID', 50);
                $table->boolean('WEB_enable')->default(false);
                $table->boolean('CVS_enable')->default(false);
                $table->boolean('ATM_enable')->default(false);
                $table->boolean('credit_enable')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 不要刪除，因為這是已存在的表
        // Schema::dropIfExists('cash_flow_settings');
    }
};
