<?php

namespace App\Http\Controllers;

use App\Models\TestingPurchasable;
use ElevateCommerce\Editor\Services\PageRenderService;
use Illuminate\Http\Request;

class TestingPurchasableWebController extends Controller
{
    protected PageRenderService $renderService;

    public function __construct(PageRenderService $renderService)
    {
        $this->renderService = $renderService;
    }

    /**
     * Display a product page
     */
    public function show(Request $request, string $slug)
    {
        // Find the product
        $product = TestingPurchasable::where('slug', $slug)
            ->where('is_active', true)
            ->with('template')
            ->firstOrFail();

        // Get template - use product's template if assigned
        if ($product->template) {
            $templateSlug = $product->template->slug;
        } else {
            // Product has no template - find default product template
            $defaultTemplate = \ElevateCommerce\Editor\Models\Template::where('model_type', TestingPurchasable::class)
                ->where('is_default', true)
                ->first();
            
            if (!$defaultTemplate) {
                abort(500, 'No default product template found');
            }
            
            $templateSlug = $defaultTemplate->slug;
        }

        // Debug logging
        \Log::info('About to render product template', [
            'template_slug' => $templateSlug,
            'product_slug' => $product->slug,
            'product_id' => $product->id,
        ]);

        try {
            // Render using page render service with dynamic template
            return $this->renderService->renderDynamicTemplate($templateSlug, $product);
        } catch (\Exception $e) {
            \Log::error('Error rendering product template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
