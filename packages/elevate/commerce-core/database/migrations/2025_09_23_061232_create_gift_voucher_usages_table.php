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
        Schema::create('gift_voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sellable_id')->constrained('sellables')->onDelete('cascade');
            $table->string('sku'); // Gift voucher SKU (e.g., VOUCHER-3)
            $table->foreignId('used_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('used_in_order_id')->constrained('orders')->onDelete('cascade');
            $table->integer('voucher_value'); // Value in cents
            $table->integer('discount_applied'); // Actual discount applied in cents (may be less than voucher value)
            $table->timestamp('used_at');
            $table->timestamps();

            // Indexes for performance
            $table->index('sku');
            $table->index(['sellable_id', 'used_by_user_id']);
            $table->index('used_at');
            
            // Unique constraint to prevent duplicate usage of same voucher
            $table->unique(['sellable_id', 'sku'], 'unique_voucher_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_voucher_usages');
    }
};
