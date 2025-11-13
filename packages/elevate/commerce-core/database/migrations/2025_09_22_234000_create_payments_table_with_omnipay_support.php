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
        // Schema::create('payments', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('order_id')->constrained()->onDelete('cascade');

        //     // Omnipay standard fields
        //     $table->string('gateway')->nullable(); // stripe, paypal, etc.
        //     $table->string('transaction_reference')->nullable(); // Omnipay transaction reference
        //     $table->string('card_reference')->nullable(); // For stored cards/token billing

        //     // Legacy Stripe fields (for backward compatibility)
        //     $table->string('stripe_payment_intent_id')->nullable();
        //     $table->string('stripe_payment_method_id')->nullable();
        //     $table->string('stripe_charge_id')->nullable();

        //     // Payment details
        //     $table->string('status'); // pending, completed, failed, authorized, requires_action, etc.
        //     $table->string('type')->default('purchase'); // purchase, authorize, capture, refund
        //     $table->string('currency', 3)->default('GBP');

        //     // Amount tracking (all in cents)
        //     $table->integer('amount'); // Original amount
        //     $table->integer('amount_authorized')->nullable(); // Amount authorized
        //     $table->integer('amount_captured')->nullable(); // Amount captured
        //     $table->integer('amount_received')->nullable(); // Amount actually received
        //     $table->integer('amount_refunded')->default(0); // Amount refunded

        //     // Error handling
        //     $table->string('failure_code')->nullable();
        //     $table->text('failure_message')->nullable();
        //     $table->text('gateway_message')->nullable(); // Gateway response message

        //     // Data storage
        //     $table->json('gateway_data')->nullable(); // Complete gateway response
        //     $table->json('stripe_data')->nullable(); // Legacy Stripe data
        //     $table->json('meta')->nullable(); // Additional metadata

        //     // Timestamps
        //     $table->timestamp('authorized_at')->nullable();
        //     $table->timestamp('captured_at')->nullable();
        //     $table->timestamp('processed_at')->nullable();
        //     $table->timestamp('expires_at')->nullable(); // For authorization expiry
        //     $table->timestamps();

        //     // Indexes for performance
        //     $table->index('gateway');
        //     $table->index('transaction_reference');
        //     $table->index('card_reference');
        //     $table->index(['order_id', 'type']);
        //     $table->index(['status', 'type']);
        //     $table->index('stripe_payment_intent_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
