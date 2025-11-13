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
        Schema::create('gift_voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_voucher_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->foreignId('used_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('used_in_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->integer('voucher_value'); // in cents
            $table->integer('discount_applied'); // in cents
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_voucher_usages');
    }
};
