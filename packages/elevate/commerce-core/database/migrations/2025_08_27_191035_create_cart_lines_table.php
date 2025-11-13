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
        Schema::create('cart_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->string('purchasable_type'); // Polymorphic type
            $table->unsignedBigInteger('purchasable_id'); // Polymorphic id
            $table->integer('quantity');
            $table->integer('unit_price'); // Price in cents
            $table->string('description');
            $table->string('identifier');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['purchasable_type', 'purchasable_id']);
            $table->index(['cart_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_lines');
    }
};
