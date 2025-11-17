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
            
            // Event details
            $table->string('event'); // e.g., order_created, status_changed, payment_received
            $table->text('description'); // Human-readable description
            $table->text('note')->nullable(); // Additional notes
            
            // Actor (who performed the action)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('user_type', ['system', 'admin', 'customer'])->default('system');
            
            // Additional data
            $table->json('metadata')->nullable(); // Event-specific data
            
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'created_at']);
            $table->index(['event', 'created_at']);
            $table->index(['user_id', 'created_at']);
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
