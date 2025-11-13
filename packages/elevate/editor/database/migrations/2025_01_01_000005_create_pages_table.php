<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            
            // Configuration
            $table->json('configuration'); // Published configuration
            $table->json('draft_configuration')->nullable(); // Draft being edited
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_tags')->nullable(); // Additional meta tags
            
            // Status
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_active')->default(true)->index();
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['theme_id', 'is_active']);
            $table->index('status');
            
            // Unique constraint: slug must be unique per theme
            $table->unique(['theme_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
