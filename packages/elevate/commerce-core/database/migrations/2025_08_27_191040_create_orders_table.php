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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->boolean('new_customer')->default(true);
            $table->string('status')->default('awaiting-payment');
            $table->string('reference')->nullable()->unique();
            $table->string('customer_reference')->nullable();
            $table->integer('sub_total')->default(0);
            $table->integer('discount_total')->default(0);
            $table->json('discount_breakdown')->nullable();
            $table->json('shipping_breakdown')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->integer('tax_total')->default(0);
            $table->integer('total')->default(0);
            $table->text('notes')->nullable();
            $table->string('currency_code', 3)->default('USD');
            $table->string('compare_currency_code', 3)->nullable();
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->timestamp('placed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['reference']);
            $table->index(['status']);
            $table->index(['placed_at']);
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
