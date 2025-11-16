<?php

namespace ElevateCommerce\Collections\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collectable extends Pivot
{
    protected $table = 'collectables';

    protected $fillable = [
        'collection_id',
        'collectable_type',
        'collectable_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent collection
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the collectable model (Product, Page, etc.)
     */
    public function collectable(): MorphTo
    {
        return $this->morphTo();
    }
}
