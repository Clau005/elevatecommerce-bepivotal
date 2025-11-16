<?php

namespace ElevateCommerce\Collections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use ElevateCommerce\Editor\Traits\HasTemplate;
use ElevateCommerce\Collections\Traits\HasDynamicCollectables;

class Collection extends Model
{
    use HasTemplate, HasDynamicCollectables;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'template_id',
        'smart_rules',
        'parent_id',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'smart_rules' => 'array',
    ];

    /**
     * Get the parent collection
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    /**
     * Get subcollections
     */
    public function children(): HasMany
    {
        return $this->hasMany(Collection::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get collection filters (many-to-many)
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(CollectionFilter::class, 'collection_filter_pivot', 'collection_id', 'filter_id')
            ->withPivot(['is_active', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Get active filters for this collection
     */
    public function activeFilters(): BelongsToMany
    {
        return $this->filters()->wherePivot('is_active', true)->orderBy('pivot_sort_order');
    }

    /**
     * Get all collectables (polymorphic)
     */
    public function collectables(): HasMany
    {
        return $this->hasMany(Collectable::class);
    }

    /**
     * Get all items regardless of type
     * Uses the dynamic collectables relationship
     */
    public function items()
    {
        return $this->collectables()->with('collectable');
    }

    /**
     * Check if this is a root collection
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Get the full path (e.g., "Electronics > Cables > USB-C")
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the URL path (e.g., "electronics/cables/usb-c")
     */
    public function getUrlPath(): string
    {
        $path = [$this->slug];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->slug);
            $parent = $parent->parent;
        }

        return implode('/', $path);
    }

    /**
     * Scope for active collections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root collections only
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
