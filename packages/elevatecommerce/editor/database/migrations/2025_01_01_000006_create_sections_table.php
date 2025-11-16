<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('category')->default('general'); // general, collection, product, etc.
            $table->text('description')->nullable();
            $table->longText('blade_code'); // Store blade template
            $table->json('schema'); // JSON schema for section settings
            $table->string('preview_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['theme_id', 'slug']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
