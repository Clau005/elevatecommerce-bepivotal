<?php

namespace Elevate\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCarrier extends Model
{
    protected $fillable = [
        'name',
        'carrier_code',
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
     * Get all enabled shipping carriers
     */
    public static function getEnabled()
    {
        return static::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
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
     * Get available shipping services for this carrier
     */
    public function getAvailableServices(): array
    {
        return $this->settings['services'] ?? [];
    }
}
