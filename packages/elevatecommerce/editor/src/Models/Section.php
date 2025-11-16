<?php

namespace ElevateCommerce\Editor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'theme_id',
        'name',
        'slug',
        'category',
        'description',
        'blade_code',
        'schema',
        'preview_image',
        'is_active',
    ];

    protected $casts = [
        'schema' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the theme this section belongs to
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
