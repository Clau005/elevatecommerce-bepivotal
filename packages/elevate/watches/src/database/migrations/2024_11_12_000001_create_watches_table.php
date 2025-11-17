<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('model_number')->nullable();
            $table->string('movement_type')->nullable(); // Automatic, Quartz, Manual
            $table->string('case_material')->nullable(); // Stainless Steel, Gold, Titanium
            $table->decimal('case_diameter', 5, 1)->nullable(); // in mm
            $table->integer('water_resistance')->nullable(); // in meters
            $table->string('strap_material')->nullable(); // Leather, Metal, Rubber
            
            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            
            // Inventory
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->decimal('weight', 8, 2)->nullable(); // in grams
            
            // Media
            $table->string('featured_image')->nullable();
            $table->json('images')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            
            // Template
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Display
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index('brand');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watches');
    }
};
