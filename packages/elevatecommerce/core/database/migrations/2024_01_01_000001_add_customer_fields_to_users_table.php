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
            $table->string('first_name')->nullable()->after('title');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('company_name')->nullable()->after('last_name');
            $table->string('account_reference')->nullable()->unique()->after('company_name');
            $table->string('tax_identifier')->nullable()->after('account_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'first_name',
                'last_name',
                'company_name',
                'account_reference',
                'tax_identifier',
            ]);
        });
    }
};
