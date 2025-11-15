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
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wishlist_id')->constrained()->onDelete('cascade');
            
            // Polymorphic relationship to any purchasable item
            $table->morphs('purchasable');
            
            $table->text('note')->nullable(); // Customer note about why they want this
            $table->tinyInteger('priority')->nullable(); // 1-5 for sorting
            $table->timestamps();

            // Indexes
            $table->index(['wishlist_id', 'created_at']);
            $table->index(['wishlist_id', 'priority']);
            // Note: morphs() already creates index on purchasable_type, purchasable_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
