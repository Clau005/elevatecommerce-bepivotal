<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collectables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->morphs('collectable'); // Creates collectable_type and collectable_id
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['collection_id', 'collectable_type', 'collectable_id']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collectables');
    }
};
