<?php

namespace Elevate\Collections\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Elevate\Collections\Models\Collection;
use Elevate\Themes\Services\UrlResolverService;
use Elevate\Themes\Services\ThemeService;

class CollectionController extends Controller
{
    protected $urlResolver;
    protected $themeService;

    public function __construct(UrlResolverService $urlResolver, ThemeService $themeService)
    {
        $this->urlResolver = $urlResolver;
        $this->themeService = $themeService;
    }

    /**
     * Show collection page
     * Resolves URL using UrlAlias and renders with theme template
     */
    public function show($path)
    {
        // Resolve URL to collection model
        $collection = $this->urlResolver->resolve($path);

        if (!$collection || !($collection instanceof Collection)) {
            abort(404);
        }

        // Check if collection is active
        if (!$collection->is_active) {
            abort(404);
        }

        // Load relationships
        $collection->load(['children', 'collectables.collectable']);

        // Render using theme's collection template
        return $this->themeService->renderDynamicTemplate('collection', $collection);
    }
}
