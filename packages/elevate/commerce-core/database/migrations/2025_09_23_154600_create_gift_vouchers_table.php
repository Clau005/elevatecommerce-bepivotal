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
        Schema::create('gift_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index(); // Unique voucher code (like SKU)
            $table->string('title'); // e.g., "£50 Christmas Gift Voucher"
            $table->text('description')->nullable();
            $table->integer('value'); // Value in cents (e.g., 5000 for £50)
            $table->string('image_url')->nullable(); // Gift voucher image
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active')->index();
            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->integer('usage_limit')->nullable(); // Total usage limit (null = unlimited)
            $table->integer('usage_count')->default(0); // Current usage count
            $table->integer('per_customer_limit')->nullable(); // Per customer usage limit
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'valid_from', 'valid_until']);
            $table->index(['is_featured', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_vouchers');
    }
};
