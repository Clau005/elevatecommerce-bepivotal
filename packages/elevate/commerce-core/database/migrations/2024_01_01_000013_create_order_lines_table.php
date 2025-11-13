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
            $table->string('purchasable_type');
            $table->unsignedBigInteger('purchasable_id');
            $table->string('type')->default('purchasable');
            $table->text('description')->nullable();
            $table->string('option')->nullable();
            $table->string('identifier')->nullable();
            $table->integer('unit_price')->default(0); // in cents
            $table->integer('unit_quantity')->default(1);
            $table->integer('quantity')->default(1);
            $table->integer('sub_total')->default(0); // in cents
            $table->text('preview')->nullable();
            $table->integer('discount_total')->default(0); // in cents
            $table->json('tax_breakdown')->nullable();
            $table->integer('tax_total')->default(0); // in cents
            $table->integer('total')->default(0); // in cents
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

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
