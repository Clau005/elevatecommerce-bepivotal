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
        Schema::create('testing_purchasables', function (Blueprint $table) {
            $table->id();
            
            // Required fields for IsPurchasable trait
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->integer('price'); // In cents
            $table->integer('compare_at_price')->nullable(); // In cents
            $table->boolean('is_active')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('track_inventory')->default(true);
            
            // Additional test fields
            $table->string('image_url')->nullable();
            $table->json('options')->nullable(); // Size, color, etc.
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('sku');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_purchasables');
    }
};
