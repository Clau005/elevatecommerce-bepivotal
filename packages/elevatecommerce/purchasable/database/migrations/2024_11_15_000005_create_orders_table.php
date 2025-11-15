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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g., ORD-20231115-0001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_email')->nullable(); // For guest checkout
            
            // Order status
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled, refunded
            
            // Pricing (all in smallest currency unit - cents)
            $table->integer('subtotal')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('shipping')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('total')->default(0);
            
            // Currency
            $table->string('currency_code', 3)->default('USD'); // ISO 4217
            
            // Payment
            $table->string('payment_method')->nullable(); // stripe, paypal, etc.
            $table->string('payment_status')->nullable(); // pending, paid, failed, refunded
            $table->string('payment_transaction_id')->nullable();
            
            // Shipping
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Notes
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['guest_email', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
