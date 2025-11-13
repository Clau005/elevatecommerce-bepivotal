<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'native_name',
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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one default language exists
        static::saving(function ($language) {
            if ($language->is_default) {
                // Set all other languages to non-default
                static::where('id', '!=', $language->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the default language.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope to get only enabled languages.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get languages ordered by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
