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
    public function __construct(
        protected TemplateRegistry $templateRegistry
    ) {}

    /**
     * Get the variable name for a model instance
     */
    public function getModelVariableName($model): ?string
    {
        return $this->templateRegistry->getVariableNameForInstance($model);
    }

    /**
     * Render a published page
     */
    public function renderPage(string $slug)
    {
        $slug = $this->normalizeSlug($slug);

        // Cache page data for performance
        $pageData = Cache::remember("page.render.{$slug}", 3600, function () use ($slug) {
            return $this->loadPageData($slug);
        });

        if (!$pageData) {
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
     * Normalize slug (homepage variations)
     */
    protected function normalizeSlug(string $slug): string
    {
        return in_array($slug, ['homepage', '', '/', 'home']) ? 'home' : $slug;
    }

    /**
     * Load page data from database
     */
    protected function loadPageData(string $slug): ?array
    {
        $activeTheme = $this->getActiveTheme();
        
        if (!$activeTheme) {
            return null;
        }

        $page = Page::where('slug', $slug)
            ->where('theme_id', $activeTheme->id)
            ->where('status', 'published')
            ->where('is_active', true)
            ->first();

        if (!$page) {
            return null;
        }

        return $this->buildPageData($page, $activeTheme);
    }

    /**
     * Get active theme (cached)
     */
    protected function getActiveTheme(): ?Theme
    {
        return Cache::remember('theme.active', 3600, function () {
            return Theme::where('is_active', true)->first();
        });
    }

    /**
     * Build page data array
     */
    protected function buildPageData($page, Theme $theme, ?array $configuration = null): array
    {
        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'title' => $page->title ?? $page->name ?? 'Page',
            'theme_id' => $theme->id,
            'theme_slug' => $theme->slug,
            'theme_name' => $theme->name,
            'configuration' => $configuration ?? $page->configuration,
            'meta_title' => $page->meta_title ?? null,
            'meta_description' => $page->meta_description ?? null,
        ];
    }

    /**
     * Render a page preview (uses draft configuration)
     */
    public function renderPreview(int $pageId, ?int $themeId = null)
    {
        $page = Page::with('theme')->findOrFail($pageId);
        $theme = $themeId ? Theme::findOrFail($themeId) : $page->theme;

        // Use draft configuration if available, otherwise published
        $configuration = $page->draft_configuration ?? $page->configuration;
        
        $pageData = $this->buildPageData($page, $theme, $configuration);

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

        if (!View::exists($layoutViewPath)) {
            abort(500, "Layout not found: {$layoutViewPath}");
        }

        $model = $pageData['model'] ?? null;
        $modelVariableName = $this->getModelVariableName($model);

        // Render all sections
        $sectionsHtml = $this->renderSections(
            $themeSlug,
            $configuration['sections'] ?? [],
            $isPreview,
            $model,
            $modelVariableName
        );

        // Build view data
        $viewData = [
            'page' => (object) $pageData,
            'model' => $model,
            'sectionsHtml' => $sectionsHtml,
            'isPreview' => $isPreview,
        ];

        if ($model && $modelVariableName) {
            $viewData[$modelVariableName] = $model;
        }

        return view($layoutViewPath, $viewData);
    }

    /**
     * Render all sections for a page
     */
    protected function renderSections(
        string $themeSlug,
        array $sections,
        bool $isPreview = false,
        $model = null,
        $modelVariableName = null
    ): string {
        $html = '';
        
        foreach ($sections as $sectionConfig) {
            // Skip hidden sections in production
            if (!$isPreview && ($sectionConfig['visible'] ?? true) === false) {
                continue;
            }
            
            $html .= $this->renderSection(
                $themeSlug,
                $sectionConfig,
                $isPreview,
                $model,
                $modelVariableName
            );
        }

        return $html;
    }

    /**
     * Render a single section
     */
    protected function renderSection(
        string $themeSlug,
        array $sectionConfig,
        bool $isPreview = false,
        $model = null,
        $modelVariableName = null
    ): string {
        $componentSlug = $sectionConfig['component'] ?? null;

        if (!$componentSlug) {
            return '';
        }

        $sectionViewPath = "themes.{$themeSlug}.sections.{$componentSlug}.index";

        if (!View::exists($sectionViewPath)) {
            if ($isPreview) {
                return "<div class='p-4 bg-red-100 border border-red-400 text-red-700'>Section not found: {$componentSlug}</div>";
            }
            return '';
        }

        try {
            $viewData = array_merge($sectionConfig['data'] ?? [], [
                'data' => $sectionConfig['data'] ?? [],
                'sectionId' => $sectionConfig['id'] ?? 'section-' . uniqid(),
                'isPreview' => $isPreview,
                'model' => $model,
            ]);

            if ($model && $modelVariableName) {
                $viewData[$modelVariableName] = $model;
            }

            return view($sectionViewPath, $viewData)->render();
        } catch (\Exception $e) {
            Log::error('Section render error', [
                'component' => $componentSlug,
                'error' => $e->getMessage(),
            ]);

            if ($isPreview) {
                return "<div class='p-4 bg-red-100 border border-red-400 text-red-700'>Error: {$componentSlug}<br>{$e->getMessage()}</div>";
            }

            return '';
        }
    }

    /**
     * Render a dynamic template with model data (for products, collections, etc.)
     */
    public function renderDynamicTemplate(string $templateSlug, $model, bool $isPreview = false)
    {
        $templateData = Cache::remember("template.render.{$templateSlug}", 3600, function () use ($templateSlug) {
            return $this->loadTemplateData($templateSlug);
        });
        
        if (!$templateData) {
            abort(404, "Template not found: {$templateSlug}");
        }

        // Verify model type if specified
        if ($templateData['model_type'] && !($model instanceof $templateData['model_type'])) {
            abort(500, "Model type mismatch for template: {$templateSlug}");
        }

        // Use draft for preview, published for live
        $configuration = $isPreview && $templateData['draft_configuration']
            ? $templateData['draft_configuration']
            : $templateData['configuration'];

        // Build page data with model
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
            'model' => $model,
        ];

        return $this->renderWithConfiguration(
            $templateData['theme_slug'],
            $configuration,
            $pageData,
            $isPreview
        );
    }

    /**
     * Load template data from database
     */
    protected function loadTemplateData(string $templateSlug): ?array
    {
        $activeTheme = $this->getActiveTheme();
        
        if (!$activeTheme) {
            return null;
        }

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
    }

    /**
     * Clear page cache
     */
    public function clearPageCache(string $slug): void
    {
        $slug = $this->normalizeSlug($slug);
        Cache::forget("page.render.{$slug}");
    }

    /**
     * Clear template cache
     */
    public function clearTemplateCache(string $slug): void
    {
        Cache::forget("template.render.{$slug}");
    }

    /**
     * Clear all editor caches
     */
    public function clearAllCaches(): void
    {
        Cache::forget('theme.active');
        // Note: Use Cache::tags() for more granular cache clearing in production
    }
}
