<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Product type: simple or variable
            $table->enum('type', ['simple', 'variable'])->default('simple');
            
            // Status: draft, active, archived
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            
            // Pricing
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost_per_item', 10, 2)->nullable();
            
            // Inventory
            $table->boolean('track_inventory')->default(false);
            $table->integer('stock')->nullable();
            
            // Shipping
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('weight_unit')->default('kg');
            $table->boolean('requires_shipping')->default(true);
            
            // Tax
            $table->boolean('is_taxable')->default(true);
            $table->decimal('tax_rate', 5, 4)->default(0);
            
            // Media
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Template
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            
            // Sorting
            $table->integer('sort_order')->default(0);
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('sku');
            $table->index('status');
            $table->index('type');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
