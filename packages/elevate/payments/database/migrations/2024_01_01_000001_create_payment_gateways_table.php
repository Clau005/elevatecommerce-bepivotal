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
            $table->string('name'); // stripe, paypal, authorize
            $table->string('display_name');
            $table->string('driver')->nullable(); // For backward compatibility
            $table->boolean('is_enabled')->default(true);
            $table->boolean('test_mode')->default(true);
            $table->text('credentials')->nullable(); // Live credentials (encrypted by Laravel)
            $table->text('test_credentials')->nullable(); // Test credentials (encrypted by Laravel)
            $table->json('settings')->nullable(); // Additional settings (not encrypted)
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_enabled');
            $table->index('sort_order');
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
