<?php

namespace Elevate\Editor\Services;

use Elevate\Editor\Models\Template;
use Illuminate\Support\Facades\Cache;

class TemplateResolver
{
    public function __construct(
        protected TemplateRegistry $registry,
        protected RenderEngine $renderEngine
    ) {}

    /**
     * Get all templates for a specific model type
     */
    public function getTemplatesForModel(string $modelClass): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("templates.{$modelClass}", 3600, function () use ($modelClass) {
            return Template::forModel($modelClass)
                ->active()
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get the default template for a model type
     */
    public function getDefaultTemplate(string $modelClass): ?Template
    {
        return Template::getDefaultForModel($modelClass);
    }

    /**
     * Resolve which template to use for a model instance
     * Priority: 1) Model's assigned template, 2) Default template for model type
     */
    public function resolveTemplateForModel($model): ?Template
    {
        $modelClass = get_class($model);

        // Check if model has an assigned template
        if (method_exists($model, 'template') && $model->template_id) {
            $template = $model->template;
            if ($template && $template->is_active && $template->status === 'published') {
                return $template;
            }
        }

        // Fall back to default template for this model type
        return $this->getDefaultTemplate($modelClass);
    }

    /**
     * Render a model using its resolved template
     */
    public function renderModel($model, bool $isPreview = false): string
    {
        $template = $this->resolveTemplateForModel($model);

        if (!$template) {
            abort(404, 'No template found for ' . class_basename($model));
        }

        return $this->renderEngine->renderTemplate($template, $model, $isPreview);
    }

    /**
     * Check if a model type has templates available
     */
    public function hasTemplates(string $modelClass): bool
    {
        return $this->getTemplatesForModel($modelClass)->isNotEmpty();
    }

    /**
     * Get template options for a dropdown (formatted for select)
     */
    public function getTemplateOptionsForModel(string $modelClass): array
    {
        $templates = $this->getTemplatesForModel($modelClass);

        return $templates->map(function ($template) {
            return [
                'value' => $template->id,
                'label' => $template->name,
                'is_default' => $template->is_default,
                'description' => $template->description,
            ];
        })->toArray();
    }

    /**
     * Clear template cache
     */
    public function clearCache(?string $modelClass = null): void
    {
        if ($modelClass) {
            Cache::forget("templates.{$modelClass}");
        } else {
            // Clear all template caches
            $registeredModels = $this->registry->all();
            foreach (array_keys($registeredModels) as $class) {
                Cache::forget("templates.{$class}");
            }
        }
    }
}
