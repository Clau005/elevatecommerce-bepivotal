<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('collections')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->enum('type', ['manual', 'smart'])->default('manual');
            $table->json('smart_rules')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'is_active']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
