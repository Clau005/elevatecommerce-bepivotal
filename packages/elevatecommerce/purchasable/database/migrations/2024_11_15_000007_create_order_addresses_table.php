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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['shipping', 'billing']); // Address type
            
            // Name
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            
            // Address
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable(); // State/Province/Region
            $table->string('postal_code');
            $table->string('country_code', 2); // ISO 3166-1 alpha-2
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
