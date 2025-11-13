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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_gateway_id')->nullable()->after('channel_id')->constrained('payment_gateways')->nullOnDelete();
            $table->foreignId('shipping_carrier_id')->nullable()->after('payment_gateway_id')->constrained('shipping_carriers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_gateway_id']);
            $table->dropForeign(['shipping_carrier_id']);
            $table->dropColumn(['payment_gateway_id', 'shipping_carrier_id']);
        });
    }
};
