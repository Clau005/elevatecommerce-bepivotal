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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('handle')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // percentage, fixed_amount, buy_x_get_y, free_shipping
            $table->decimal('value', 10, 2)->nullable(); // percentage or fixed amount
            $table->string('coupon_code')->nullable()->unique(); // null for auto-apply discounts
            $table->boolean('is_active')->default(true);
            $table->boolean('is_automatic')->default(false); // auto-apply vs coupon code
            $table->integer('usage_limit')->nullable(); // null for unlimited
            $table->integer('usage_limit_per_customer')->nullable(); // null for unlimited
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable(); // cap for percentage discounts
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->integer('priority')->default(0); // for applying multiple discounts
            $table->json('buy_x_get_y_config')->nullable(); // config for buy X get Y discounts
            $table->boolean('combine_with_other_discounts')->default(false);
            $table->json('meta')->nullable(); // additional configuration
            $table->timestamps();
            
            $table->index(['is_active', 'starts_at', 'expires_at']);
            $table->index(['is_automatic']);
            $table->index(['coupon_code']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
