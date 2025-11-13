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
        Schema::table('users', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('first_name')->after('title');
            $table->string('last_name')->after('first_name');
            $table->string('company_name')->nullable()->after('last_name');
            $table->string('account_reference')->unique()->nullable()->after('company_name');
            $table->string('tax_identifier')->nullable()->after('account_reference');
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete()->after('tax_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_group_id']);
            $table->dropColumn([
                'title',
                'first_name',
                'last_name',
                'company_name',
                'account_reference',
                'tax_identifier',
                'customer_group_id',
            ]);
        });
    }
};
