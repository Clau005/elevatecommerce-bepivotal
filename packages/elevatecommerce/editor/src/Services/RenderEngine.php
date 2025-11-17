<?php

namespace ElevateCommerce\Editor\Services;

use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Template;
use ElevateCommerce\Editor\Models\Theme;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RenderEngine
{
    /**
     * Render a page
     */
    public function renderPage(Page $page, bool $isPreview = false): string
    {
        $config = $page->getConfigurationForRender($isPreview);
        $theme = $page->theme;

        return $this->renderWithConfiguration($theme, $config, $page, null, $isPreview);
    }

    /**
     * Render a template with model data
     */
    public function renderTemplate(Template $template, $model, bool $isPreview = false): string
    {
        $config = $template->getConfigurationForRender($isPreview);
        $theme = Theme::active();

        if (!$theme) {
            abort(500, 'No active theme found');
        }

        // Verify model type matches template
        if ($template->model_type && !($model instanceof $template->model_type)) {
            abort(500, "Model type mismatch for template: {$template->slug}");
        }

        return $this->renderWithConfiguration($theme, $config, null, $model, $isPreview);
    }

    /**
     * Core rendering logic
     */
    protected function renderWithConfiguration(
        Theme $theme,
        array $config,
        ?Page $page = null,
        $model = null,
        bool $isPreview = false
    ): string {
        // Get layout
        $layout = $config['layout'] ?? 'default';
        
        // Build sections HTML
        $sectionsHtml = '';
        if (isset($config['sections']) && is_array($config['sections'])) {
            foreach ($config['sections'] as $sectionConfig) {
                $sectionsHtml .= $this->renderSection($theme, $sectionConfig, $model, $isPreview);
            }
        }

        // Prepare data for layout
        $layoutData = [
            'page' => $page,
            'model' => $model,
            'sectionsHtml' => $sectionsHtml,
            'theme' => $theme,
        ];

        // Add SEO data
        if ($page) {
            $layoutData['meta_title'] = $page->meta_title ?? $page->title;
            $layoutData['meta_description'] = $page->meta_description ?? $page->excerpt;
        } elseif ($model) {
            $layoutData['meta_title'] = $model->meta_title ?? ($model->name ?? $model->title ?? '');
            $layoutData['meta_description'] = $model->meta_description ?? ($model->description ?? '');
        }

        // Render layout
        $layoutViewPath = "themes.{$theme->slug}.layouts.{$layout}";
        
        if (!View::exists($layoutViewPath)) {
            abort(500, "Layout not found: {$layoutViewPath}");
        }

        return view($layoutViewPath, $layoutData)->render();
    }

    /**
     * Render a single section
     */
    protected function renderSection(
        Theme $theme,
        array $sectionConfig,
        $model = null,
        bool $isPreview = false
    ): string {
        $componentSlug = $sectionConfig['component'] ?? $sectionConfig['type'] ?? null;
        
        if (!$componentSlug) {
            Log::warning('Section configuration missing component/type', $sectionConfig);
            return '';
        }

        $sectionData = $sectionConfig['data'] ?? [];
        $sectionId = $sectionConfig['id'] ?? 'section-' . uniqid();

        // Inject model data
        if ($model) {
            $sectionData = $this->processTemplateVariables($sectionData, $model);
            $sectionData['model'] = $model;
        }

        // Render the component
        $viewPath = "themes.{$theme->slug}.sections.{$componentSlug}.index";
        
        if (!View::exists($viewPath)) {
            Log::error("Section view not found: {$viewPath}");
            return "<!-- Section not found: {$componentSlug} -->";
        }

        try {
            $html = view($viewPath, $sectionData)->render();

            // Wrap with editor controls if preview mode
            if ($isPreview) {
                return $this->wrapWithEditorControls($html, $sectionId, $componentSlug);
            }

            return $html;
        } catch (\Exception $e) {
            Log::error("Section rendering error: {$componentSlug}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return "<!-- Section error: {$componentSlug} - {$e->getMessage()} -->";
        }
    }

    /**
     * Process template variables in data (e.g., {{model.name}})
     */
    protected function processTemplateVariables(array $data, $model): array
    {
        array_walk_recursive($data, function (&$value) use ($model) {
            if (is_string($value) && preg_match('/\{\{(.+?)\}\}/', $value, $matches)) {
                $path = trim($matches[1]);
                $value = $this->resolveModelPath($model, $path);
            }
        });

        return $data;
    }

    /**
     * Resolve a dot-notation path on a model (e.g., "model.name" or "model.category.name")
     */
    protected function resolveModelPath($model, string $path)
    {
        $parts = explode('.', $path);
        $current = $model;

        foreach ($parts as $part) {
            if ($part === 'model') {
                continue; // Skip the "model" prefix
            }

            if (is_object($current) && isset($current->$part)) {
                $current = $current->$part;
            } elseif (is_array($current) && isset($current[$part])) {
                $current = $current[$part];
            } else {
                return $path; // Return original if path doesn't resolve
            }
        }

        return $current;
    }

    /**
     * Wrap section HTML with editor controls for preview mode
     */
    protected function wrapWithEditorControls(string $html, string $sectionId, string $componentSlug): string
    {
        return <<<HTML
<div class="editor-section" 
     data-section-id="{$sectionId}" 
     data-component="{$componentSlug}"
     onclick="window.parent.postMessage({type: 'selectSection', sectionId: '{$sectionId}'}, '*')"
     style="position: relative; cursor: pointer; transition: all 0.2s ease;"
     onmouseover="this.style.outline='2px solid #3b82f6'; this.style.outlineOffset='2px';"
     onmouseout="this.style.outline='none';">
    {$html}
    <div class="editor-section-label" 
         style="position: absolute; top: 8px; right: 8px; background: rgba(59, 130, 246, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; pointer-events: none;">
        {$componentSlug}
    </div>
</div>
HTML;
    }

    /**
     * Get available sections for a theme
     */
    public function getAvailableSections(Theme $theme): array
    {
        return Cache::remember("theme.{$theme->slug}.sections", 3600, function () use ($theme) {
            return $theme->getAvailableSections();
        });
    }
}
