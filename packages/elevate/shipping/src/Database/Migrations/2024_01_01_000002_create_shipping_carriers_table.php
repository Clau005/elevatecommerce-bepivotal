<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('carrier_code'); // ups, fedex, usps, dhl, etc.
            $table->boolean('is_enabled')->default(false);
            $table->boolean('test_mode')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('credentials'); // encrypted JSON
            $table->text('test_credentials')->nullable(); // encrypted JSON
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_carriers');
    }
};
