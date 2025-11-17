<?php

namespace ElevateCommerce\Editor\Services;

use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Section;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PageRenderService
{
    /**
     * Model type to variable name mapping
     * Can be extended via config or service provider
     */
    protected array $modelVariableMap = [
        \ElevateCommerce\Collections\Models\Collection::class => 'collection',
        \Elevate\Product\Models\Product::class => 'product',
    ];

    /**
     * Get the variable name for a model type
     */
    public function getModelVariableName($model): ?string
    {
        if (!$model) {
            return null;
        }

        $modelClass = get_class($model);
        
        // Check direct mapping
        if (isset($this->modelVariableMap[$modelClass])) {
            return $this->modelVariableMap[$modelClass];
        }

        // Check parent classes
        foreach ($this->modelVariableMap as $class => $variableName) {
            if ($model instanceof $class) {
                return $variableName;
            }
        }

        return null;
    }

    /**
     * Register a model type mapping
     */
    public function registerModelType(string $modelClass, string $variableName): void
    {
        $this->modelVariableMap[$modelClass] = $variableName;
    }

    /**
     * Render a published page
     */
    public function renderPage(string $slug)
    {
        // Normalize homepage slug
        if ($slug === 'homepage' || $slug === '' || $slug === '/') {
            $slug = 'homepage';
        }

        Log::info('PageRenderService: renderPage called', [
            'slug' => $slug,
        ]);

        // Cache page data for performance
        $cacheKey = "editor.page.render.{$slug}";
        
        $pageData = Cache::remember($cacheKey, 3600, function () use ($slug) {
            // Get active theme
            $activeTheme = Theme::where('is_active', true)->first();
            
            if (!$activeTheme) {
                return null;
            }

            // Get published page for active theme
            $page = Page::where('slug', $slug)
                ->where('theme_id', $activeTheme->id)
                ->where('status', 'published')
                ->where('is_active', true)
                ->first();

            if (!$page) {
                return null;
            }

            $pageData = [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->title,
                'theme_id' => $activeTheme->id,
                'theme_slug' => $activeTheme->slug,
                'theme_name' => $activeTheme->name,
                'configuration' => $page->configuration,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
            ];

            Log::info('PageRenderService: page data loaded from DB', [
                'page_id' => $pageData['id'],
                'slug' => $pageData['slug'],
                'theme_slug' => $pageData['theme_slug'],
                'configuration' => $pageData['configuration'],
            ]);

            return $pageData;
        });

        if (!$pageData) {
            Log::error('PageRenderService: page not found', ['slug' => $slug]);
            abort(404, "Page not found: {$slug}");
        }

        return $this->renderWithConfiguration(
            $pageData['theme_slug'],
            $pageData['configuration'],
            $pageData,
            false
        );
    }

    /**
     * Render a page preview (uses draft configuration)
     */
    public function renderPreview(int $pageId, ?int $themeId = null)
    {
        $page = Page::with('theme')->findOrFail($pageId);

        // Use specified theme or page's theme
        $theme = $themeId ? Theme::findOrFail($themeId) : $page->theme;

        // Use draft configuration if available, otherwise published
        $configuration = $page->has_draft_configuration 
            ? $page->draft_configuration 
            : $page->configuration;

        $pageData = [
            'id' => $page->id,
            'slug' => $page->slug,
            'title' => $page->title,
            'theme_id' => $theme->id,
            'theme_slug' => $theme->slug,
            'theme_name' => $theme->name,
            'configuration' => $configuration,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
        ];

        return $this->renderWithConfiguration(
            $theme->slug,
            $configuration,
            $pageData,
            true
        );
    }

    /**
     * Render page with given configuration
     */
    protected function renderWithConfiguration(
        string $themeSlug,
        array $configuration,
        array $pageData,
        bool $isPreview = false
    ) {
        $layout = $configuration['basic_info']['layout'] ?? 'default';
        $layoutViewPath = "themes.{$themeSlug}.layouts.{$layout}";

        Log::info('PageRenderService: renderWithConfiguration', [
            'theme_slug' => $themeSlug,
            'layout' => $layout,
            'layout_view_path' => $layoutViewPath,
            'sections_count' => count($configuration['sections'] ?? []),
            'sections' => $configuration['sections'] ?? [],
        ]);

        if (!View::exists($layoutViewPath)) {
            abort(500, "Layout not found: {$layoutViewPath}");
        }

        // Extract model if present (for dynamic templates)
        $model = $pageData['model'] ?? null;

        // Determine specific variable name based on model type
        $modelVariableName = $this->getModelVariableName($model);

        // Render all sections
        $sectionsHtml = $this->renderSections(
            $themeSlug,
            $configuration['sections'] ?? [],
            $isPreview,
            $model,
            $modelVariableName
        );

        Log::info('PageRenderService: sections rendered', [
            'sections_html_length' => strlen($sectionsHtml),
            'sections_html_preview' => substr($sectionsHtml, 0, 200),
        ]);

        // Build page object for view
        $page = (object) $pageData;

        // Build view data with specific model variable name
        $viewData = [
            'page' => $page,
            'model' => $model, // Keep for backward compatibility
            'sectionsHtml' => $sectionsHtml,
            'isPreview' => $isPreview,
        ];

        // Add model with specific variable name
        if ($model && $modelVariableName) {
            $viewData[$modelVariableName] = $model;
        }

        return view($layoutViewPath, $viewData);
    }

    /**
     * Render all sections for a page
     */
    protected function renderSections(string $themeSlug, array $sections, bool $isPreview = false, $model = null, $modelVariableName = null): string
    {
        Log::info('PageRenderService: renderSections called', [
            'theme_slug' => $themeSlug,
            'sections_count' => count($sections),
            'is_preview' => $isPreview,
            'has_model' => $model !== null,
            'model_variable_name' => $modelVariableName,
        ]);

        $html = '';
        foreach ($sections as $index => $sectionConfig) {
            Log::info("PageRenderService: rendering section {$index}", [
                'section_config' => $sectionConfig,
            ]);
            
            $sectionHtml = $this->renderSection($themeSlug, $sectionConfig, $isPreview, $model, $modelVariableName);
            
            Log::info("PageRenderService: section {$index} rendered", [
                'html_length' => strlen($sectionHtml),
                'html_preview' => substr($sectionHtml, 0, 100),
            ]);
            
            $html .= $sectionHtml;
        }

        Log::info('PageRenderService: all sections rendered', [
            'total_html_length' => strlen($html),
        ]);

        return $html;
    }

    /**
     * Render a single section
     */
    protected function renderSection(string $themeSlug, array $sectionConfig, bool $isPreview = false, $model = null, $modelVariableName = null): string
    {
        $componentSlug = $sectionConfig['component'] ?? null;

        Log::info('PageRenderService: renderSection called', [
            'theme_slug' => $themeSlug,
            'component_slug' => $componentSlug,
            'section_config' => $sectionConfig,
            'has_model' => $model !== null,
            'model_variable_name' => $modelVariableName,
        ]);

        if (!$componentSlug) {
            Log::warning('Section missing component slug', ['config' => $sectionConfig]);
            return '';
        }

        $sectionData = $sectionConfig['data'] ?? [];
        $sectionId = $sectionConfig['id'] ?? 'section-' . uniqid();

        // Build view path: themes.{theme-slug}.sections.{component-slug}.index
        $sectionViewPath = "themes.{$themeSlug}.sections.{$componentSlug}.index";

        Log::info('PageRenderService: checking view existence', [
            'view_path' => $sectionViewPath,
            'view_exists' => View::exists($sectionViewPath),
        ]);

        if (!View::exists($sectionViewPath)) {
            Log::error('Section view not found', [
                'view_path' => $sectionViewPath,
                'component' => $componentSlug,
                'theme' => $themeSlug,
            ]);
            
            if ($isPreview) {
                return "<div class='p-4 bg-red-100 border border-red-400 text-red-700'>Section not found: {$componentSlug}</div>";
            }
            
            return '';
        }

        try {
            Log::info('PageRenderService: rendering view', [
                'view_path' => $sectionViewPath,
                'section_data' => $sectionData,
            ]);

            // Pass both the data array AND extract variables for direct access
            $viewData = array_merge($sectionData, [
                'data' => $sectionData,
                'sectionId' => $sectionId,
                'isPreview' => $isPreview,
                'model' => $model, // Pass model to sections for dynamic templates (backward compatibility)
            ]);

            // Add model with specific variable name
            if ($model && $modelVariableName) {
                $viewData[$modelVariableName] = $model;
            }

            $rendered = view($sectionViewPath, $viewData)->render();

            Log::info('PageRenderService: view rendered successfully', [
                'html_length' => strlen($rendered),
                'html_preview' => substr($rendered, 0, 150),
            ]);

            return $rendered;
        } catch (\Exception $e) {
            Log::error('Section render error', [
                'component' => $componentSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($isPreview) {
                return "<div class='p-4 bg-red-100 border border-red-400 text-red-700'>Error rendering section: {$componentSlug}<br>{$e->getMessage()}</div>";
            }

            return '';
        }
    }

    /**
     * Render a dynamic template with model data (for products, collections, etc.)
     */
    public function renderDynamicTemplate(string $templateSlug, $model, bool $isPreview = false)
    {
        // Use cache for template data (invalidated on publish)
        $cacheKey = "editor.template.render.{$templateSlug}";
        
        $templateData = Cache::remember($cacheKey, 3600, function () use ($templateSlug) {
            // Get the active theme
            $activeTheme = Theme::where('is_active', true)->first();
            
            if (!$activeTheme) {
                return null;
            }

            // Find the template
            $template = \ElevateCommerce\Editor\Models\Template::where('slug', $templateSlug)
                ->first();

            if (!$template) {
                return null;
            }
            
            return [
                'id' => $template->id,
                'slug' => $template->slug,
                'name' => $template->name,
                'model_type' => $template->model_type,
                'configuration' => $template->configuration,
                'draft_configuration' => $template->draft_configuration,
                'theme_id' => $activeTheme->id,
                'theme_slug' => $activeTheme->slug,
                'theme_name' => $activeTheme->name,
            ];
        });
        
        if (!$templateData) {
            abort(404, "Template not found: {$templateSlug}");
        }

        // Verify model type if specified
        if ($templateData['model_type'] && !($model instanceof $templateData['model_type'])) {
            abort(500, "Model type mismatch for template: {$templateSlug}");
        }

        // Use draft configuration for preview, published for live site
        $configuration = $isPreview && $templateData['draft_configuration']
            ? $templateData['draft_configuration']
            : $templateData['configuration'];

        // Build page data for rendering
        $pageData = [
            'id' => $templateData['id'],
            'slug' => $model->slug ?? $templateSlug,
            'title' => $model->name ?? $model->title ?? 'Page',
            'theme_id' => $templateData['theme_id'],
            'theme_slug' => $templateData['theme_slug'],
            'theme_name' => $templateData['theme_name'],
            'configuration' => $configuration,
            'meta_title' => $model->meta_title ?? null,
            'meta_description' => $model->meta_description ?? null,
            'model' => $model, // Pass the model for template access
        ];

        return $this->renderWithConfiguration(
            $templateData['theme_slug'],
            $configuration,
            $pageData,
            $isPreview
        );
    }

    /**
     * Clear page cache
     */
    public function clearPageCache(string $slug): void
    {
        Cache::forget("editor.page.render.{$slug}");
    }

    /**
     * Clear all page caches
     */
    public function clearAllPageCaches(): void
    {
        Cache::flush();
    }
}
