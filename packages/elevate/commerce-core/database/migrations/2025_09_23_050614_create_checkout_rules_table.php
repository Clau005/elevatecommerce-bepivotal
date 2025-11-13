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
            $table->enum('type', [
                'free_shipping_threshold',
                'minimum_order_amount',
                'maximum_order_amount',
                'quantity_discount',
                'category_discount',
                'customer_group_discount',
                'first_order_discount',
                'bulk_discount'
            ]);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            
            // Condition fields
            $table->decimal('threshold_amount', 10, 2)->nullable(); // For free shipping, min/max order
            $table->integer('threshold_quantity')->nullable(); // For quantity-based rules
            $table->json('conditions')->nullable(); // Complex conditions
            
            // Action fields
            $table->enum('action_type', [
                'free_shipping',
                'percentage_discount',
                'fixed_discount',
                'gift_item',
                'upgrade_shipping'
            ]);
            $table->decimal('action_value', 10, 2)->nullable(); // Discount amount/percentage
            $table->json('action_config')->nullable(); // Additional action configuration
            
            // Restrictions
            $table->json('customer_groups')->nullable(); // Specific customer groups
            $table->json('product_categories')->nullable(); // Specific categories
            $table->json('excluded_products')->nullable(); // Excluded products
            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();
            
            // Usage tracking
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['priority', 'is_active']);
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
