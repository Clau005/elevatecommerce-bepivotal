<?php

namespace ElevateCommerce\Editor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'author_url',
        'preview_image',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get pages for this theme
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the theme path for file-based assets
     */
    public function getPathAttribute(): string
    {
        return config('editor.themes_path') . '/' . $this->slug;
    }

    /**
     * Check if theme has a specific layout
     */
    public function hasLayout(string $layout): bool
    {
        $layoutPath = $this->path . '/layouts/' . $layout . '.blade.php';
        return file_exists($layoutPath);
    }

    /**
     * Get available sections for this theme
     */
    public function getAvailableSections(): array
    {
        $sectionsPath = $this->path . '/sections';
        
        if (!is_dir($sectionsPath)) {
            return [];
        }

        $sections = [];
        $directories = glob($sectionsPath . '/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $sectionSlug = basename($dir);
            $schemaFile = $dir . '/schema.json';

            if (file_exists($schemaFile)) {
                $schema = json_decode(file_get_contents($schemaFile), true);
                $sections[] = [
                    'slug' => $sectionSlug,
                    'name' => $schema['name'] ?? ucfirst(str_replace('-', ' ', $sectionSlug)),
                    'description' => $schema['description'] ?? '',
                    'category' => $schema['category'] ?? 'General',
                    'thumbnail' => $schema['thumbnail'] ?? null,
                    'schema' => $schema,
                ];
            }
        }

        return $sections;
    }

    /**
     * Activate this theme (deactivate others)
     */
    public function activate(): void
    {
        static::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * Get the active theme
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
