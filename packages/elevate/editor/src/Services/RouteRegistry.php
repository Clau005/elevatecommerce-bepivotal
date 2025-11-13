<?php

namespace Elevate\Editor\Services;

use Illuminate\Support\Facades\Cache;
use Elevate\Editor\Models\Page;
use Elevate\Editor\Models\Theme;

class RouteRegistry
{
    protected const CACHE_KEY_PAGES = 'editor.route_registry.pages';
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Reserved paths that should never be used for pages
     */
    protected static array $reservedPaths = [
        'admin',
        'api',
        'products',
        'watches',
        'collections',
        'cart',
        'checkout',
        'account',
        'login',
        'register',
        'logout',
        'password',
        'preview',
    ];

    /**
     * Get all page slugs for the active theme
     */
    public function getPageSlugs(): array
    {
        return Cache::remember(self::CACHE_KEY_PAGES, self::CACHE_TTL, function () {
            $activeTheme = Theme::where('is_active', true)->first();

            if (!$activeTheme) {
                return [];
            }

            return Page::where('theme_id', $activeTheme->id)
                ->where('status', 'published')
                ->where('is_active', true)
                ->pluck('slug')
                ->toArray();
        });
    }

    /**
     * Check if a slug is a page
     */
    public function isPage(string $slug): bool
    {
        return in_array($slug, $this->getPageSlugs());
    }

    /**
     * Check if a slug is reserved
     */
    public function isReserved(string $slug): bool
    {
        return in_array($slug, self::$reservedPaths);
    }

    /**
     * Get all reserved paths
     */
    public function getReservedPaths(): array
    {
        return self::$reservedPaths;
    }

    /**
     * Clear page cache
     */
    public function clearPageCache(): void
    {
        Cache::forget(self::CACHE_KEY_PAGES);
    }

    /**
     * Register a page slug (clears cache)
     */
    public function registerPage(string $slug): void
    {
        $this->clearPageCache();
    }

    /**
     * Unregister a page slug (clears cache)
     */
    public function unregisterPage(string $slug): void
    {
        $this->clearPageCache();
    }
}
