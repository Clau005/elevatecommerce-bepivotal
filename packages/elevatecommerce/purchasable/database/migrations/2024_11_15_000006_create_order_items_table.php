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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relationship to any purchasable item
            $table->morphs('purchasable');
            
            // Snapshot data (captured at time of order)
            $table->string('name'); // Product name snapshot
            $table->string('sku')->nullable(); // SKU snapshot
            
            // Pricing (all in smallest currency unit - cents)
            $table->integer('quantity')->default(1);
            $table->integer('price'); // Selling price snapshot
            $table->integer('cost_price')->default(0); // Cost price snapshot
            $table->integer('tax')->default(0); // Tax for this line item
            $table->integer('discount')->default(0); // Discount for this line item
            
            // Additional data
            $table->json('options')->nullable(); // Size, color, custom text, etc.
            $table->json('metadata')->nullable(); // Full snapshot of purchasable data
            
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'created_at']);
            // Note: morphs() already creates index on purchasable_type, purchasable_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
