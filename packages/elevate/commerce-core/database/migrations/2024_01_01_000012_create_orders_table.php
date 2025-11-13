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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('payment_gateway_id')->nullable();
            $table->unsignedBigInteger('shipping_carrier_id')->nullable();
            $table->boolean('new_customer')->default(false);
            $table->string('status')->default('awaiting-payment');
            $table->string('reference')->unique();
            $table->string('customer_reference')->nullable();
            $table->integer('sub_total')->default(0); // in cents
            $table->integer('discount_total')->default(0); // in cents
            $table->json('discount_breakdown')->nullable();
            $table->integer('gift_voucher_total')->default(0); // in cents
            $table->json('gift_voucher_breakdown')->nullable();
            $table->json('shipping_breakdown')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->integer('tax_total')->default(0); // in cents
            $table->integer('total')->default(0); // in cents
            $table->text('notes')->nullable();
            $table->string('currency_code', 3)->default('GBP');
            $table->string('compare_currency_code', 3)->nullable();
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->timestamp('placed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('reference');
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
