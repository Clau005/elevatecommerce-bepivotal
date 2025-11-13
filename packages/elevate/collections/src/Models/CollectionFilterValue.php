<?php

namespace Elevate\Collections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionFilterValue extends Model
{
    protected $fillable = [
        'filter_id',
        'label',
        'slug',
        'value',
        'product_count',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'product_count' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the filter this value belongs to
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(CollectionFilter::class, 'filter_id');
    }

    /**
     * Scope to get active values
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
