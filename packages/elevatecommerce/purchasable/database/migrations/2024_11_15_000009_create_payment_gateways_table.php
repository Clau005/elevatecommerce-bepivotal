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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique(); // 'stripe', 'paypal'
            $table->string('name'); // Display name
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Font Awesome icon class
            
            // Status
            $table->boolean('enabled')->default(false);
            $table->boolean('test_mode')->default(true);
            
            // Display settings
            $table->integer('sort_order')->default(0);
            
            // Additional settings (non-sensitive)
            $table->json('settings')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['enabled', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
