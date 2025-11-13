<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            $table->string('name')->nullable();
            $table->string('sku')->nullable()->unique();
            
            // Pricing
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost_per_item', 10, 2)->nullable();
            
            // Inventory
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock')->nullable();
            
            // Shipping
            $table->decimal('weight', 10, 2)->nullable();
            
            // Media
            $table->string('image')->nullable();
            
            // Variant options (up to 3 options)
            $table->string('option1_name')->nullable(); // e.g., "Size"
            $table->string('option1_value')->nullable(); // e.g., "Large"
            $table->string('option2_name')->nullable(); // e.g., "Color"
            $table->string('option2_value')->nullable(); // e.g., "Red"
            $table->string('option3_name')->nullable(); // e.g., "Material"
            $table->string('option3_value')->nullable(); // e.g., "Cotton"
            
            // Sorting and status
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('product_id');
            $table->index('sku');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
