<?php

namespace ElevateCommerce\Collections\Http\Controllers;

use ElevateCommerce\Collections\Models\Collection;
use ElevateCommerce\Collections\Services\FilterService;
use ElevateCommerce\Editor\Services\PageRenderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionWebController extends Controller
{
    protected PageRenderService $renderService;
    protected FilterService $filterService;

    public function __construct(PageRenderService $renderService, FilterService $filterService)
    {
        $this->renderService = $renderService;
        $this->filterService = $filterService;
    }

    /**
     * Display a collection page with optional filters
     */
    public function show(Request $request, string $parent = null, ?string $child = null, ?string $filters = null)
    {
        // Determine which slug to use based on parameters
        // If child is provided, check if it's actually a nested collection or a filter
        $slug = $parent;
        $parentSlug = null;
        $filterSegments = [];
        
        if ($child) {
            // Check if child is a valid collection slug
            $childCollection = Collection::where('slug', $child)
                ->where('is_active', true)
                ->first();
            
            if ($childCollection) {
                // It's a nested collection
                $slug = $child;
                $parentSlug = $parent;
                $filterSegments = $filters ? explode('/', $filters) : [];
            } else {
                // Child is actually a filter, not a collection
                // Treat parent as the collection and child+filters as filter segments
                $slug = $parent;
                $parentSlug = null;
                $filterSegments = array_filter([$child, $filters]);
                if (count($filterSegments) > 0) {
                    // Flatten if filters contains slashes
                    $filterSegments = explode('/', implode('/', $filterSegments));
                }
            }
        } else {
            // Single-level collection
            $filterSegments = $filters ? explode('/', $filters) : [];
        }
        
        // Exclude reserved paths
        $reservedPaths = ['admin', 'api', 'products', 'cart', 'checkout', 'account'];
        if ($parentSlug && in_array($parentSlug, $reservedPaths)) {
            abort(404);
        }
        
        // Build query
        $query = Collection::where('slug', $slug)
            ->where('is_active', true)
            ->with(['collectables.collectable', 'template', 'parent', 'children', 'filters.values']);

        
        // If parent slug is provided, ensure this is a child of that parent
        if ($parentSlug) {
            $parentCollection = Collection::where('slug', $parentSlug)
                ->where('is_active', true)
                ->firstOrFail();
            
            $query->where('parent_id', $parentCollection->id);
        }
        
        $collection = $query->firstOrFail();

        
        // Parse and validate filters
        $appliedFilters = $this->filterService->parseFilterSegments($filterSegments, $collection);
        
        // Check if filter path is canonical (alphabetically sorted)
        // If not, redirect to canonical URL
        if (!empty($appliedFilters) && !$this->filterService->isCanonicalPath($filterSegments, $appliedFilters)) {
            $canonicalPath = $this->filterService->getCanonicalFilterPath($appliedFilters);
            $baseUrl = $parentSlug ? "/{$parentSlug}/{$slug}" : "/{$slug}";
            return redirect($baseUrl . '/' . $canonicalPath, 301);
        }

        // Get active theme
        $activeTheme = \ElevateCommerce\Editor\Models\Theme::where('is_active', true)->first();
        
        if (!$activeTheme) {
            abort(500, 'No active theme found');
        }
        // Get template - use collection's template if assigned
        if ($collection->template) {
            $templateSlug = $collection->template->slug;
        } else {
            // Collection has no template - find default collection template
            $defaultTemplate = \ElevateCommerce\Editor\Models\Template::where('model_type', 'ElevateCommerce\Collections\Models\Collection')
                ->where('slug', 'collection-default')
                ->where('is_default', true)
                ->first();
            
            if (!$defaultTemplate) {
                abort(500, 'No default collection template found');
            }
            
            $templateSlug = $defaultTemplate->slug;
        }

        // Get all collection items (polymorphic)
        $collectables = $collection->collectables()
            ->with('collectable')
            ->orderBy('sort_order')
            ->get();
        
        // Extract the actual items from the collectables
        $items = $collectables->map(function($collectable) {
            return $collectable->collectable;
        })->filter(); // Remove any null items
        
        // Use collection's per_page setting, fallback to config, then default to 12
        $perPage = $collection->per_page ?? config('collections.pagination.per_page', 12);
        $currentPage = request()->get('page', 1);
        $itemsForPage = $items->forPage($currentPage, $perPage);
        
        // Create a paginator
        $items = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForPage,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
        
        // Get available filters (if applicable)
        $baseUrl = $parentSlug ? "/{$parentSlug}/{$slug}" : "/{$slug}";
        $availableFilters = []; // Filters would need to be implemented for polymorphic items

        // Add data to collection for template access
        $collection->items = $items;
        $collection->breadcrumbs = $this->buildBreadcrumbs($collection);
        $collection->applied_filters = $appliedFilters;
        $collection->available_filters = $availableFilters;
        $collection->base_url = $baseUrl;
        $collection->filter_service = $this->filterService; // For generating filter URLs in template

        // Debug logging
        \Log::info('About to render collection template', [
            'template_slug' => $templateSlug,
            'collection_slug' => $collection->slug,
            'items_count' => $items->count(),
            'items_type' => get_class($items),
        ]);

        try {
            // Render using page render service with dynamic template
            return $this->renderService->renderDynamicTemplate($templateSlug, $collection);
        } catch (\Exception $e) {
            \Log::error('Error rendering collection template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Build breadcrumbs for collection
     */
    protected function buildBreadcrumbs(Collection $collection): array
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => '/'],
        ];

        // Add parent collections if nested
        if ($collection->parent) {
            $parents = [];
            $current = $collection->parent;
            
            while ($current) {
                array_unshift($parents, $current);
                $current = $current->parent;
            }

            foreach ($parents as $parent) {
                $breadcrumbs[] = [
                    'name' => $parent->name,
                    'url' => "/{$parent->slug}",
                ];
            }
        }

        // Add current collection
        $breadcrumbs[] = [
            'name' => $collection->name,
            'url' => null, // Current page
        ];

        return $breadcrumbs;
    }
}
