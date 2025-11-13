<?php

namespace Elevate\Collections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollectionFilter extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'source_model',
        'source_column',
        'source_relation',
        'sort_order',
        'is_active',
        'config',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Get the collections this filter belongs to
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_filter_pivot', 'filter_id', 'collection_id')
            ->withPivot(['is_active', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Get all filter values
     */
    public function values(): HasMany
    {
        return $this->hasMany(CollectionFilterValue::class, 'filter_id');
    }

    /**
     * Get active filter values
     */
    public function activeValues(): HasMany
    {
        return $this->values()->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope to get active filters
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
