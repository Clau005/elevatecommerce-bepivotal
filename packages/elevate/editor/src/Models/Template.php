<?php

namespace Elevate\Editor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'model_type',
        'description',
        'configuration',
        'draft_configuration',
        'preview_image',
        'status',
        'is_active',
        'is_default',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'draft_configuration' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['icon'];

    /**
     * Get the icon for this template's model type from the registry
     */
    public function getIconAttribute(): ?string
    {
        if (!$this->model_type) {
            return null;
        }

        $registry = app(\Elevate\Editor\Services\TemplateRegistry::class);
        $config = $registry->get($this->model_type);
        
        return $config['icon'] ?? null;
    }

    /**
     * Get editor sessions for this template
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(EditorSession::class);
    }

    /**
     * Get versions for this template
     */
    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class);
    }

    /**
     * Publish the template (move draft to live)
     */
    public function publish(int $userId, ?string $changeNotes = null): void
    {
        // Create version before publishing
        $this->versions()->create([
            'created_by' => $userId,
            'version_number' => $this->versions()->max('version_number') + 1,
            'configuration' => $this->configuration,
            'change_notes' => $changeNotes,
        ]);

        // Publish
        $this->update([
            'configuration' => $this->draft_configuration ?? $this->configuration,
            'draft_configuration' => null,
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Clear cache
        cache()->forget("template.{$this->slug}");
        cache()->forget("templates.{$this->model_type}");
    }

    /**
     * Set as default template for this model type
     */
    public function setAsDefault(): void
    {
        // Remove default from other templates of same model type
        static::where('model_type', $this->model_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Scope to get templates for a specific model type
     */
    public function scopeForModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'published');
    }

    /**
     * Get configuration for rendering (draft in preview, published in live)
     */
    public function getConfigurationForRender(bool $isPreview = false): array
    {
        if ($isPreview && $this->draft_configuration) {
            return $this->draft_configuration;
        }

        return $this->configuration ?? [];
    }

    /**
     * Get the default template for a model type
     */
    public static function getDefaultForModel(string $modelType): ?self
    {
        return static::forModel($modelType)
            ->active()
            ->where('is_default', true)
            ->first();
    }
}
