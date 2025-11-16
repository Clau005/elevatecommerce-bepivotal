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
        Schema::create('collection_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_id')->constrained('collection_filters')->onDelete('cascade');
            $table->string('label'); // Display label
            $table->string('slug'); // URL-friendly slug
            $table->string('value'); // Actual filter value
            $table->integer('product_count')->default(0); // Number of products with this value
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('filter_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_filter_values');
    }
};
