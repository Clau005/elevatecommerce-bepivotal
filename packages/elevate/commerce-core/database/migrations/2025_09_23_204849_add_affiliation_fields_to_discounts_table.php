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
        Schema::table('discounts', function (Blueprint $table) {
            $table->foreignId('affiliation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('event_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('apply_to', ['all', 'first'])->nullable();
            
            // Add index for better performance
            $table->index(['affiliation_id', 'event_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropForeign(['affiliation_id']);
            $table->dropForeign(['event_type_id']);
            $table->dropIndex(['affiliation_id', 'event_type_id']);
            $table->dropColumn(['affiliation_id', 'event_type_id', 'apply_to']);
        });
    }
};
