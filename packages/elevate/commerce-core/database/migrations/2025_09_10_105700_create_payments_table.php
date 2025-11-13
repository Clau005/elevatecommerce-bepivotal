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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_payment_method_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'succeeded',
                'failed',
                'canceled',
                'requires_action',
                'requires_payment_method'
            ])->default('pending');
            $table->enum('type', ['payment', 'refund', 'partial_refund'])->default('payment');
            $table->string('currency', 3)->default('gbp');
            $table->integer('amount'); // Amount in cents
            $table->integer('amount_received')->nullable(); // Actual amount received in cents
            $table->integer('amount_refunded')->default(0); // Amount refunded in cents
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->json('stripe_data')->nullable(); // Store full Stripe response
            $table->json('meta')->nullable(); // Additional metadata
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['order_id', 'status']);
            $table->index(['stripe_payment_intent_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};