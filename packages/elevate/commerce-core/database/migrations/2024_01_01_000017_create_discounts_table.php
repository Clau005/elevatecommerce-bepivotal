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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('handle')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // percentage, fixed_amount, free_shipping, buy_x_get_y
            $table->decimal('value', 10, 2)->nullable(); // percentage or amount
            $table->string('coupon_code')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_automatic')->default(false);
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_limit_per_customer')->nullable();
            $table->integer('minimum_order_amount')->nullable(); // in cents
            $table->integer('maximum_discount_amount')->nullable(); // in cents
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('priority')->default(0);
            $table->json('buy_x_get_y_config')->nullable();
            $table->boolean('combine_with_other_discounts')->default(false);
            $table->unsignedBigInteger('affiliation_id')->nullable();
            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->string('apply_to')->nullable(); // order, shipping, products
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('coupon_code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
