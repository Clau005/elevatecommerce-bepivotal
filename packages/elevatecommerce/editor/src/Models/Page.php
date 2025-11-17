<?php

namespace ElevateCommerce\Editor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;
    
    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        // Clear page slug cache when pages are created, updated, or deleted
        static::saved(function () {
            \ElevateCommerce\Editor\Services\PageSlugRegistry::clearCache();
        });
        
        static::deleted(function () {
            \ElevateCommerce\Editor\Services\PageSlugRegistry::clearCache();
        });
    }

    protected $fillable = [
        'theme_id',
        'title',
        'slug',
        'excerpt',
        'configuration',
        'draft_configuration',
        'meta_title',
        'meta_description',
        'meta_tags',
        'status',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'draft_configuration' => 'array',
        'meta_tags' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the theme this page belongs to
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Publish the page (move draft to live)
     */
    public function publish(): void
    {
        $this->update([
            'configuration' => $this->draft_configuration ?? $this->configuration,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Get the URL for this page
     */
    public function getUrlAttribute(): string
    {
        return '/' . $this->slug;
    }

    /**
     * Scope to get only published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('is_active', true);
    }

    /**
     * Get configuration for rendering (draft in preview, published in live)
     */
    public function getConfigurationForRender(bool $isPreview = false): array
    {
        if ($isPreview && $this->draft_configuration) {
            return $this->draft_configuration;
        }

        return $this->configuration ?? [];
    }
}
