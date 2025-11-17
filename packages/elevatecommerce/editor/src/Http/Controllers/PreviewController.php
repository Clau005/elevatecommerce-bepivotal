<?php

namespace ElevateCommerce\Editor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Template;
use ElevateCommerce\Editor\Services\PageRenderService;

class PreviewController extends Controller
{
    protected PageRenderService $renderService;

    public function __construct(PageRenderService $renderService)
    {
        $this->renderService = $renderService;
    }
    /**
     * Preview a page with draft configuration
     */
    public function page(Request $request, Theme $theme, Page $page)
    {
        // Check if config is passed in request (for live updates)
        if ($request->has('config')) {
            $config = json_decode($request->get('config'), true);
            // Store in session for iframe reloads
            session(['editor.preview.page.' . $page->id => $config]);
        } else {
            // Try session first
            $config = session('editor.preview.page.' . $page->id);
            
            if (!$config) {
                // Use draft_configuration if available
                if ($page->draft_configuration && !empty($page->draft_configuration)) {
                    $config = $page->draft_configuration;
                } else {
                    // Fall back to published configuration
                    $config = $page->configuration;
                }
            }
        }

        // Ensure config is an array
        if (!is_array($config)) {
            $config = [];
        }

        // Render sections
        $sectionsHtml = $this->renderSections($theme->slug, $config['sections'] ?? [], $page);

        // Get layout
        $layout = $config['basic_info']['layout'] ?? 'default';
        $layoutViewPath = "themes.{$theme->slug}.layouts.{$layout}";

        if (!View::exists($layoutViewPath)) {
            abort(500, "Layout not found: {$layoutViewPath}");
        }

        // Build page object
        $pageObject = (object) [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
        ];

        return view($layoutViewPath, [
            'page' => $pageObject,
            'sectionsHtml' => $sectionsHtml,
            'isPreview' => true,
        ]);
    }

    /**
     * Preview a template with draft configuration
     */
    public function template(Request $request, Theme $theme, Template $template)
    {
        // Check if config is passed in request
        if ($request->has('config')) {
            $config = json_decode($request->get('config'), true);
            session(['editor.preview.template.' . $template->id => $config]);
        } else {
            // Try session first
            $config = session('editor.preview.template.' . $template->id);
            
            if (!$config) {
                // Use draft_configuration if available
                if ($template->draft_configuration && !empty($template->draft_configuration)) {
                    $config = $template->draft_configuration;
                } else {
                    // Fall back to published configuration
                    $config = $template->configuration;
                }
            }
        }

        // Ensure config is an array
        if (!is_array($config)) {
            $config = [];
        }

        // Get a sample model for preview (first product, collection, etc.)
        $model = $this->getSampleModel($template->model_type, $template->id);

        if (!$model) {
            return response('No sample data available for this template type', 404);
        }

        // Render sections
        $sectionsHtml = $this->renderSections($theme->slug, $config['sections'] ?? [], $model);

        // Get layout
        $layout = $config['layout'] ?? 'default';
        $layoutViewPath = "themes.{$theme->slug}.layouts.{$layout}";

        if (!View::exists($layoutViewPath)) {
            abort(500, "Layout not found: {$layoutViewPath}");
        }

        return view($layoutViewPath, [
            'page' => $model,
            'model' => $model,
            'sectionsHtml' => $sectionsHtml,
            'isPreview' => true,
        ]);
    }

    /**
     * Render sections HTML
     */
    protected function renderSections(string $themeSlug, array $sections, $model = null): string
    {
        $html = '';

        foreach ($sections as $sectionConfig) {
            $componentSlug = $sectionConfig['component'] ?? null;
            
            if (!$componentSlug) {
                continue;
            }

            $sectionData = $sectionConfig['data'] ?? [];
            $sectionId = $sectionConfig['id'] ?? 'section-' . uniqid();

            // Add model to section data
            if ($model) {
                $sectionData['model'] = $model;
                
                // Also add with specific variable name based on model type
                // Use PageRenderService's method which is extensible
                $modelVariableName = $this->renderService->getModelVariableName($model);
                if ($modelVariableName) {
                    $sectionData[$modelVariableName] = $model;
                }
            }

            // Build view path
            $sectionViewPath = "themes.{$themeSlug}.sections.{$componentSlug}.index";

            if (View::exists($sectionViewPath)) {
                try {
                    // Pass both the data array AND extract variables for direct access
                    $viewData = array_merge($sectionData, [
                        'data' => $sectionData,
                        'sectionId' => $sectionId,
                        'isPreview' => true,
                    ]);
                    
                    $html .= view($sectionViewPath, $viewData)->render();
                } catch (\Exception $e) {
                    Log::error('Preview section render error', [
                        'component' => $componentSlug,
                        'error' => $e->getMessage(),
                    ]);
                    
                    $html .= "<div class='p-4 bg-red-100 border border-red-400 text-red-700'>Error rendering section: {$componentSlug}</div>";
                }
            }
        }

        return $html;
    }

    /**
     * Get a sample model for template preview
     */
    protected function getSampleModel(string $modelType, ?int $templateId = null)
    {
        if (!class_exists($modelType)) {
            return null;
        }

        // Try to find a model that uses this specific template
        $query = $modelType::query();
        
        if ($templateId) {
            // First try to find a model with this template
            $model = $query->where('template_id', $templateId)->inRandomOrder()->first();
            
            // If no model uses this template, fall back to any model
            if (!$model) {
                $model = $modelType::inRandomOrder()->first();
            }
        } else {
            $model = $query->inRandomOrder()->first();
        }
        
        if (!$model) {
            return null;
        }

        // Load relationships based on model type
        if ($modelType === 'ElevateCommerce\Collections\Models\Collection') {
            // Load collectables with their related models
            $model->load(['collectables.collectable', 'children', 'filters.values']);
            
            // Manually set the dynamic relationships by accessing them
            // This triggers the __get magic method and caches the results
            try {
                // Access watches to trigger loading
                $watches = $model->watches;
                // Access products to trigger loading
                $products = $model->products;
                // Access pages to trigger loading  
                $pages = $model->pages;
            } catch (\Exception $e) {
                \Log::warning('Error loading dynamic collection relationships in preview', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $model;
    }
}
