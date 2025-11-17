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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relationship to any purchasable item
            $table->morphs('purchasable');
            
            $table->integer('quantity')->default(1);
            $table->integer('price'); // Snapshot price in smallest currency unit (cents)
            $table->json('options')->nullable(); // Size, color, custom text, etc.
            $table->timestamps();

            // Indexes
            $table->index(['cart_id', 'created_at']);
            // Note: morphs() already creates index on purchasable_type, purchasable_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
