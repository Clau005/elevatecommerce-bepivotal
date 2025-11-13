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
        Schema::create('order_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who made the entry (staff or customer)
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null'); // If staff made the entry
            $table->string('type')->default('comment'); // comment, status_change, order_created, order_updated, payment_received, etc.
            $table->string('title')->nullable(); // Brief title for the event
            $table->text('content')->nullable(); // Comment content or event description
            $table->json('data')->nullable(); // Additional data for system events (old_status, new_status, etc.)
            $table->boolean('is_system_event')->default(false); // true for automatic events, false for manual comments
            $table->boolean('is_visible_to_customer')->default(true); // whether customer can see this entry
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
            $table->index(['type']);
            $table->index(['is_system_event']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_timelines');
    }
};
