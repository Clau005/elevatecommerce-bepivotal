<?php

namespace ElevateCommerce\Core\Support\Helpers;

use ElevateCommerce\Core\Models\Currency;

class CurrencyHelper
{
    /**
     * Format amount in smallest currency unit to display format
     */
    public static function format(int $amountInCents, ?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? Currency::where('code', $currencyCode)->first() 
            : Currency::getDefault();

        if (!$currency) {
            $currency = Currency::getGBPFallback();
        }

        return $currency->format($amountInCents);
    }

    /**
     * Get the default currency
     */
    public static function getDefault(): Currency
    {
        return Currency::getDefault();
    }

    /**
     * Convert amount to cents/smallest unit
     */
    public static function toCents(float $amount, ?string $currencyCode = null): int
    {
        $currency = $currencyCode 
            ? Currency::where('code', $currencyCode)->first() 
            : Currency::getDefault();

        if (!$currency) {
            $currency = Currency::getGBPFallback();
        }

        return $currency->toCents($amount);
    }
}
