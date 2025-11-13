<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'iso3',
        'numeric_code',
        'phone_code',
        'is_enabled',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the states for this country.
     */
    public function states(): HasMany
    {
        return $this->hasMany('Elevate\CommerceCore\Models\State');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one default country exists
        static::saving(function ($country) {
            if ($country->is_default) {
                // Set all other countries to non-default
                static::where('id', '!=', $country->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the default country.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope to get only enabled countries.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get countries ordered by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
