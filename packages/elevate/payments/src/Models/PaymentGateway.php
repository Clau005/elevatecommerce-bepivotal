<?php

namespace Elevate\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'driver',
        'is_enabled',
        'test_mode',
        'sort_order',
        'credentials',
        'test_credentials',
        'settings',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'test_credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_enabled' => 'boolean',
        'test_mode' => 'boolean',
    ];

    /**
     * Get all enabled payment gateways
     */
    public static function getEnabled()
    {
        return static::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get available payment methods for this gateway
     */
    public function getAvailablePaymentMethods(): array
    {
        return $this->settings['payment_methods'] ?? [];
    }

    /**
     * Get the appropriate credentials based on test mode
     */
    public function getActiveCredentials(): array
    {
        if ($this->test_mode && !empty($this->test_credentials)) {
            return $this->test_credentials;
        }
        
        return $this->credentials ?? [];
    }

    /**
     * Check if gateway supports a specific payment method
     */
    public function supportsPaymentMethod(string $method): bool
    {
        return in_array($method, $this->getAvailablePaymentMethods());
    }

    /**
     * Get all transactions for this gateway
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'gateway', 'name');
    }
}
