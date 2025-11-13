<?php

namespace Elevate\Watches\Http\Controllers;

use Elevate\Watches\Models\Watch;
use Elevate\Editor\Services\PageRenderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WatchWebController extends Controller
{
    protected $renderService;

    public function __construct(PageRenderService $renderService)
    {
        $this->renderService = $renderService;
    }

    /**
     * Display a watch page
     */
    public function show(Request $request, string $slug)
    {
        // Find watch by slug
        $watch = Watch::where('slug', $slug)
            ->where('status', 'active')
            ->with(['template'])
            ->firstOrFail();

        // Get active theme
        $activeTheme = \Elevate\Editor\Models\Theme::where('is_active', true)->first();
        
        if (!$activeTheme) {
            abort(500, 'No active theme found');
        }

        // Get template - use watch's template if assigned
        if ($watch->template) {
            $templateSlug = $watch->template->slug;
        } else {
            // Watch has no template - find default watch template
            $defaultTemplate = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Watches\Models\Watch')
                ->where('is_default', true)
                ->whereIn('slug', ['watch-default', 'watch', 'product-default', 'product'])
                ->first();
            
            if (!$defaultTemplate) {
                abort(500, 'No default watch template found');
            }
            
            $templateSlug = $defaultTemplate->slug;
        }

        // Add breadcrumbs to watch for template access
        $watch->breadcrumbs = $this->buildBreadcrumbs($watch);

        // Add related watches
        $watch->related_watches = $this->getRelatedWatches($watch);

        // Render using theme service with dynamic template
        // Pass as 'product' variable for compatibility with product-show template
        return $this->renderService->renderDynamicTemplate($templateSlug, $watch, 'product');
    }

    /**
     * Build breadcrumbs for watch
     */
    protected function buildBreadcrumbs(Watch $watch): array
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => '/'],
            ['name' => 'Watches', 'url' => '/watches'],
        ];

        // Add current watch
        $breadcrumbs[] = [
            'name' => $watch->name,
            'url' => null, // Current page
        ];

        return $breadcrumbs;
    }

    /**
     * Get related watches
     */
    protected function getRelatedWatches(Watch $watch, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        // Get watches with similar brand, excluding current watch
        return Watch::where('status', 'active')
            ->where('brand', $watch->brand)
            ->where('id', '!=', $watch->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
