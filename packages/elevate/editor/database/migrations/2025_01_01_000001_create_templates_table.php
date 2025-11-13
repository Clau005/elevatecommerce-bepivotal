<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('model_type')->index(); // Fully qualified class name
            $table->text('description')->nullable();
            
            // Configuration
            $table->json('configuration'); // Published configuration
            $table->json('draft_configuration')->nullable(); // Draft being edited
            
            // Metadata
            $table->string('preview_image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false); // Default template for this model type
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['model_type', 'is_active']);
            $table->index(['model_type', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
