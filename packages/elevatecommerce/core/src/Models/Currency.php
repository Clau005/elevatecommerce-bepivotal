<?php

namespace ElevateCommerce\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_default',
        'is_enabled',
        'exchange_rate',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_enabled' => 'boolean',
        'decimal_places' => 'integer',
        'exchange_rate' => 'decimal:6',
    ];

    /**
     * Get the default currency
     */
    public static function getDefault(): self
    {
        return static::where('is_default', true)
            ->where('is_enabled', true)
            ->first() ?? static::getGBPFallback();
    }

    /**
     * Get GBP fallback currency
     */
    public static function getGBPFallback(): self
    {
        $gbp = static::where('code', 'GBP')->first();
        
        if (!$gbp) {
            // Create GBP as fallback if it doesn't exist
            $gbp = static::create([
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'decimal_places' => 2,
                'is_default' => true,
                'is_enabled' => true,
                'exchange_rate' => 1.00,
            ]);
        }

        return $gbp;
    }

    /**
     * Format an amount in smallest currency unit to display format
     */
    public function format(int $amountInCents): string
    {
        $amount = $amountInCents / (10 ** $this->decimal_places);
        
        return $this->symbol . number_format(
            $amount,
            $this->decimal_places,
            '.',
            ','
        );
    }

    /**
     * Convert amount to cents/smallest unit
     */
    public function toCents(float $amount): int
    {
        return (int) round($amount * (10 ** $this->decimal_places));
    }

    /**
     * Set this currency as default
     */
    public function setAsDefault(): void
    {
        // Remove default from all other currencies
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one default currency
        static::saving(function ($currency) {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }
}
