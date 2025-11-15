<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO 4217 code (e.g., GBP, USD, EUR)
            $table->string('name'); // Full name (e.g., British Pound)
            $table->string('symbol', 10); // Currency symbol (e.g., £, $, €)
            $table->unsignedTinyInteger('decimal_places')->default(2); // Number of decimal places
            $table->boolean('is_default')->default(false); // Is this the default currency?
            $table->boolean('is_enabled')->default(true); // Is this currency enabled?
            $table->decimal('exchange_rate', 10, 6)->default(1.000000); // Exchange rate relative to base
            $table->timestamps();

            $table->index('is_default');
            $table->index('is_enabled');
        });

        // Insert GBP as default fallback
        DB::table('currencies')->insert([
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'decimal_places' => 2,
            'is_default' => true,
            'is_enabled' => true,
            'exchange_rate' => 1.000000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
