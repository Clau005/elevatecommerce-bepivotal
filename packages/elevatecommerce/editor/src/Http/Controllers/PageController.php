<?php

namespace ElevateCommerce\Editor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use ElevateCommerce\Editor\Services\PageRenderService;
use ElevateCommerce\Editor\Services\RouteRegistry;
use ElevateCommerce\Collections\Models\Collection;
use ElevateCommerce\Collections\Http\Controllers\CollectionWebController;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function __construct(
        protected PageRenderService $renderService,
        protected RouteRegistry $routeRegistry
    ) {}

    /**
     * Show a page by slug
     */
    public function show(string $slug)
    {
        $start = microtime(true);
        // Check if slug is reserved (admin, api, etc.)
        if ($this->routeRegistry->isReserved($slug)) {
            abort(404);
        }

        // Handle nested routes (collection/subcollection) or single collection slugs
        if (str_contains($slug, '/') || $this->isCollectionSlug($slug)) {
            return $this->delegateToCollectionController($slug);
        }

        // // Render as a page
        // return $this->renderService->renderPage($slug);

         // Render as a page
        $response = $this->renderService->renderPage($slug);
        
        $totalTime = (microtime(true) - $start) * 1000;
        Log::info('Page render complete', [
            'slug' => $slug,
            'total_ms' => round($totalTime, 2)
        ]);
        
        return $response;
        
    }

    /**
     * Check if slug belongs to an active collection (cached)
     */
    protected function isCollectionSlug(string $slug): bool
    {
        return Cache::remember("collection.exists.{$slug}", 3600, function () use ($slug) {
            return Collection::where('slug', $slug)
                ->where('is_active', true)
                ->exists();
        });
    }

    /**
     * Delegate to collection controller
     */
    protected function delegateToCollectionController(string $slug)
    {
        $parts = explode('/', $slug);
        $parent = $parts[0];
        $child = $parts[1] ?? null;
        $additionalFilters = isset($parts[2]) ? implode('/', array_slice($parts, 2)) : null;
        
        return app(CollectionWebController::class)->show(
            request(),
            $parent,
            $child,
            $additionalFilters
        );
    }

    /**
     * Preview a page (uses draft configuration)
     */
    public function preview(Request $request)
    {
        $pageId = $request->input('page_id');
        $themeId = $request->input('theme_id');

        if (!$pageId) {
            abort(400, 'Page ID is required for preview');
        }

        return $this->renderService->renderPreview($pageId, $themeId);
    }
}
