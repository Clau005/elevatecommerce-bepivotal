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
        Schema::table('gift_voucher_usages', function (Blueprint $table) {
            // Add new columns
            $table->foreignId('gift_voucher_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('code')->after('gift_voucher_id')->index();
            
            // Drop old columns
            $table->dropForeign(['sellable_id']);
            $table->dropColumn(['sellable_id', 'sku']);
            
            // Update indexes
            $table->index(['code', 'used_by_user_id']);
            $table->index(['gift_voucher_id', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_voucher_usages', function (Blueprint $table) {
            // Add back old columns
            $table->foreignId('sellable_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('sku')->after('sellable_id');
            
            // Drop new columns
            $table->dropForeign(['gift_voucher_id']);
            $table->dropColumn(['gift_voucher_id', 'code']);
            
            // Restore old indexes
            $table->index(['sku', 'used_by_user_id']);
        });
    }
};
