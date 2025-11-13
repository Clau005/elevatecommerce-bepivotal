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
        Schema::create('checkout_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('handle')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // free_shipping_threshold, minimum_order_amount, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->decimal('threshold_amount', 10, 2)->nullable();
            $table->integer('threshold_quantity')->nullable();
            $table->json('conditions')->nullable();
            $table->string('action_type'); // free_shipping, percentage_discount, fixed_discount
            $table->decimal('action_value', 10, 2)->nullable();
            $table->json('action_config')->nullable();
            $table->json('customer_groups')->nullable();
            $table->json('product_categories')->nullable();
            $table->json('excluded_products')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_rules');
    }
};
