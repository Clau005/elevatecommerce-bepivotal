<?php

namespace Elevate\CommerceCore\Services;

use Elevate\CommerceCore\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    protected ?Currency $activeCurrency = null;
    
    /**
     * Get the active currency (default or session-based)
     */
    public function getActiveCurrency(): Currency
    {
        if ($this->activeCurrency) {
            return $this->activeCurrency;
        }
        
        // Try to get from session first (for multi-currency support)
        $currencyCode = session('currency_code');
        
        if ($currencyCode) {
            $currency = Cache::remember("currency_{$currencyCode}", 3600, function () use ($currencyCode) {
                return Currency::where('code', $currencyCode)
                    ->where('is_enabled', true)
                    ->first();
            });
            
            if ($currency) {
                $this->activeCurrency = $currency;
                return $currency;
            }
        }
        
        // Fall back to default currency
        $this->activeCurrency = Cache::remember('currency_default', 3600, function () {
            return Currency::where('is_default', true)->first() 
                ?? Currency::where('is_enabled', true)->first();
        });
        
        return $this->activeCurrency;
    }
    
    /**
     * Format amount in active currency
     * Amount should be in smallest unit (pence/cents)
     */
    public function format(int $amountInSmallestUnit, ?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode)
            : $this->getActiveCurrency();
            
        return $currency->formatAmount($amountInSmallestUnit);
    }
    
    /**
     * Format amount without symbol
     */
    public function formatWithoutSymbol(int $amountInSmallestUnit, ?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode)
            : $this->getActiveCurrency();
            
        return $currency->formatAmountWithoutSymbol($amountInSmallestUnit);
    }
    
    /**
     * Get currency symbol
     */
    public function symbol(?string $currencyCode = null): string
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode)
            : $this->getActiveCurrency();
            
        return $currency->symbol;
    }
    
    /**
     * Convert amount from smallest unit to decimal
     */
    public function toDecimal(int $amountInSmallestUnit, ?string $currencyCode = null): float
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode)
            : $this->getActiveCurrency();
            
        return $currency->toDecimal($amountInSmallestUnit);
    }
    
    /**
     * Convert decimal amount to smallest unit
     */
    public function toSmallestUnit(float $decimalAmount, ?string $currencyCode = null): int
    {
        $currency = $currencyCode 
            ? $this->getCurrencyByCode($currencyCode)
            : $this->getActiveCurrency();
            
        return $currency->toSmallestUnit($decimalAmount);
    }
    
    /**
     * Get currency by code
     */
    protected function getCurrencyByCode(string $code): Currency
    {
        return Cache::remember("currency_{$code}", 3600, function () use ($code) {
            return Currency::where('code', $code)
                ->where('is_enabled', true)
                ->firstOrFail();
        });
    }
    
    /**
     * Set active currency for the session
     */
    public function setActiveCurrency(string $currencyCode): void
    {
        $currency = Currency::where('code', $currencyCode)
            ->where('is_enabled', true)
            ->firstOrFail();
            
        session(['currency_code' => $currencyCode]);
        $this->activeCurrency = $currency;
    }
    
    /**
     * Get all enabled currencies
     */
    public function getEnabledCurrencies()
    {
        return Cache::remember('currencies_enabled', 3600, function () {
            return Currency::where('is_enabled', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }
}
