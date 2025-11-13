<?php

namespace Elevate\Product\Http\Controllers;

use Elevate\Product\Models\Product;
use Elevate\Editor\Services\PageRenderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductWebController extends Controller
{
    protected $renderService;

    public function __construct(PageRenderService $renderService)
    {
        $this->renderService = $renderService;
    }

    /**
     * Display a product page
     */
    public function show(Request $request, string $slug)
    {
        // Find product by slug
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['variants', 'template'])
            ->firstOrFail();

        // Get active theme
        $activeTheme = \Elevate\Editor\Models\Theme::where('is_active', true)->first();
        
        if (!$activeTheme) {
            abort(500, 'No active theme found');
        }

        // Get template - use product's template if assigned
        if ($product->template) {
            $templateSlug = $product->template->slug;
        } else {
            // Product has no template - find default product template
            $defaultTemplate = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Product\Models\Product')
                ->where('is_default', true)
                ->whereIn('slug', ['product-default', 'product'])
                ->first();
            
            if (!$defaultTemplate) {
                abort(500, 'No default product template found');
            }
            
            $templateSlug = $defaultTemplate->slug;
        }

        // Add breadcrumbs to product for template access
        $product->breadcrumbs = $this->buildBreadcrumbs($product);

        // Add related products (products from same category or similar)
        $product->related_products = $this->getRelatedProducts($product);

        // Render using theme service with dynamic template
        return $this->renderService->renderDynamicTemplate($templateSlug, $product);
    }

    /**
     * Build breadcrumbs for product
     */
    protected function buildBreadcrumbs(Product $product): array
    {
        $breadcrumbs = [
            ['name' => 'Home', 'url' => '/'],
            ['name' => 'Products', 'url' => '/products'],
        ];

        // Add current product
        $breadcrumbs[] = [
            'name' => $product->name,
            'url' => null, // Current page
        ];

        return $breadcrumbs;
    }

    /**
     * Get related products
     */
    protected function getRelatedProducts(Product $product, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        // Get products with similar type, excluding current product
        return Product::where('status', 'active')
            ->where('type', $product->type)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
