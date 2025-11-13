<?php

namespace Elevate\Editor\Traits;

use Elevate\Editor\Models\Template;
use Elevate\Editor\Services\TemplateResolver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasTemplate
{
    /**
     * Boot the trait
     */
    public static function bootHasTemplate(): void
    {
        // Add template_id to fillable if not already there
        static::creating(function ($model) {
            if (!in_array('template_id', $model->getFillable())) {
                $model->fillable[] = 'template_id';
            }
        });
    }

    /**
     * Get the template relationship
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get available templates for this model type
     */
    public static function getAvailableTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        $resolver = app(TemplateResolver::class);
        return $resolver->getTemplatesForModel(static::class);
    }

    /**
     * Get the default template for this model type
     */
    public static function getDefaultTemplate(): ?Template
    {
        $resolver = app(TemplateResolver::class);
        return $resolver->getDefaultTemplate(static::class);
    }

    /**
     * Render this model using its assigned template
     */
    public function render(bool $isPreview = false): string
    {
        $resolver = app(TemplateResolver::class);
        return $resolver->renderModel($this, $isPreview);
    }

    /**
     * Get data to pass to the template
     * Override this in your model to customize what data is available
     */
    public function getTemplateData(): array
    {
        return $this->toArray();
    }

    /**
     * Get template options for dropdown (formatted for select)
     */
    public static function getTemplateOptions(): array
    {
        $resolver = app(TemplateResolver::class);
        return $resolver->getTemplateOptionsForModel(static::class);
    }

    /**
     * Assign a template to this model
     */
    public function assignTemplate(?int $templateId): void
    {
        $this->update(['template_id' => $templateId]);
    }

    /**
     * Check if this model has a template assigned
     */
    public function hasTemplate(): bool
    {
        return !is_null($this->template_id);
    }

    /**
     * Get the resolved template (assigned or default)
     */
    public function getResolvedTemplate(): ?Template
    {
        $resolver = app(TemplateResolver::class);
        return $resolver->resolveTemplateForModel($this);
    }
}
