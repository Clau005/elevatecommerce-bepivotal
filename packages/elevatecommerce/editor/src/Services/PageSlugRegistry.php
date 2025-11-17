<?php

namespace ElevateCommerce\Editor\Services;

use Illuminate\Support\Facades\Cache;
use ElevateCommerce\Editor\Models\Page;

class PageSlugRegistry
{
    protected const CACHE_KEY = 'editor.page_slugs_pattern';
    
    /**
     * Get the regex pattern for all page slugs
     */
    public static function getPattern(): string
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::buildPattern();
        });
    }
    
    /**
     * Build the regex pattern from database
     */
    protected static function buildPattern(): string
    {
        $slugs = Page::where('is_active', true)
            ->pluck('slug')
            ->unique()
            ->filter()
            ->toArray();
        
        if (empty($slugs)) {
            // Return pattern that matches nothing if no pages exist
            return '(?!)';
        }
        
        // Escape special regex characters in slugs
        $escapedSlugs = array_map(function($slug) {
            return preg_quote($slug, '/');
        }, $slugs);
        
        return implode('|', $escapedSlugs);
    }
    
    /**
     * Clear the cached pattern
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
    
    /**
     * Refresh the pattern (clear and rebuild)
     */
    public static function refresh(): void
    {
        self::clearCache();
        self::getPattern();
    }
}
