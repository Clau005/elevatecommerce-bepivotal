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
        Schema::create('wishlist_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wishlist_id');
            $table->string('purchasable_type');
            $table->unsignedBigInteger('purchasable_id');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('wishlist_id')->references('id')->on('wishlists')->onDelete('cascade');
            $table->index(['purchasable_type', 'purchasable_id']);
            $table->unique(['wishlist_id', 'purchasable_type', 'purchasable_id'], 'unique_wishlist_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_lines');
    }
};
