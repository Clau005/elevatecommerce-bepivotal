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
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('purchasable_type'); // Polymorphic type
            $table->unsignedBigInteger('purchasable_id'); // Polymorphic id
            $table->string('type')->default('purchasable');
            $table->string('description');
            $table->string('option')->nullable();
            $table->string('identifier');
            $table->integer('unit_price'); // Price in cents
            $table->integer('unit_quantity')->default(1);
            $table->integer('quantity');
            $table->integer('sub_total')->default(0);
            $table->integer('discount_total')->default(0);
            $table->json('tax_breakdown')->nullable();
            $table->integer('tax_total')->default(0);
            $table->integer('total')->default(0);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['purchasable_type', 'purchasable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
