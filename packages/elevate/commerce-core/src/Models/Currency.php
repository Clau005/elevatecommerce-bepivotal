<?php

namespace Elevate\CommerceCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_enabled',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
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

        // Ensure only one default currency exists
        static::saving(function ($currency) {
            if ($currency->is_default) {
                // Set all other currencies to non-default
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the default currency.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope to get only enabled currencies.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get currencies ordered by sort order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Format amount in this currency.
     */
    public function formatAmount($amount): string
    {
        return $this->symbol . number_format($amount / 100, 2);
    }
}
