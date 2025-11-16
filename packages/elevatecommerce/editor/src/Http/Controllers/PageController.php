<?php

namespace ElevateCommerce\Editor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ElevateCommerce\Editor\Services\PageRenderService;
use ElevateCommerce\Editor\Services\RouteRegistry;
use Elevate\Collections\Models\Collection;
use Elevate\Collections\Http\Controllers\CollectionWebController;

class PageController extends Controller
{
    protected PageRenderService $renderService;
    protected RouteRegistry $routeRegistry;

    public function __construct(PageRenderService $renderService, RouteRegistry $routeRegistry)
    {
        $this->renderService = $renderService;
        $this->routeRegistry = $routeRegistry;
    }

    /**
     * Show a page by slug
     */
    public function show(string $slug)
    {
        \Log::info('Editor PageController::show called', [
            'slug' => $slug,
            'url' => request()->url(),
        ]);

        // If slug contains a slash, it's a nested route (collection/subcollection)
        // Parse and delegate to collection controller
        if (str_contains($slug, '/')) {
            \Log::info('Slug contains slash, delegating to CollectionWebController');
            
            $parts = explode('/', $slug);
            $parent = $parts[0];
            $child = $parts[1] ?? null;
            $additionalFilters = isset($parts[2]) ? implode('/', array_slice($parts, 2)) : null;
            
            $collectionController = app(CollectionWebController::class);
            return $collectionController->show(request(), $parent, $child, $additionalFilters);
        }

        // Check if slug is reserved
        if ($this->routeRegistry->isReserved($slug)) {
            abort(404);
        }

        // First, check if this slug belongs to an active collection
        $collection = Collection::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($collection) {
            \Log::info('Collection found, delegating to CollectionWebController', [
                'collection_slug' => $collection->slug,
            ]);
            
            // Delegate to CollectionWebController
            $collectionController = app(CollectionWebController::class);
            return $collectionController->show(request(), $slug, null, null);
        }

        \Log::info('No collection found, rendering as page', ['slug' => $slug]);

        // Otherwise, render as a page
        return $this->renderService->renderPage($slug);
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
