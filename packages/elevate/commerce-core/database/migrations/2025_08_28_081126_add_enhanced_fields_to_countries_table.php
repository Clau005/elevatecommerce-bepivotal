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
        Schema::table('countries', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(true)->after('phone_code');
            $table->boolean('is_default')->default(false)->after('is_enabled');
            $table->integer('sort_order')->default(0)->after('is_default');
            
            $table->index('is_enabled');
            $table->index('is_default');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropIndex(['is_enabled']);
            $table->dropIndex(['is_default']);
            $table->dropIndex(['sort_order']);
            $table->dropColumn(['is_enabled', 'is_default', 'sort_order']);
        });
    }
};
