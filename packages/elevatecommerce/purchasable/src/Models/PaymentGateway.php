<?php

namespace ElevateCommerce\Purchasable\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'payment_gateways';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'gateway',
        'name',
        'description',
        'icon',
        'enabled',
        'test_mode',
        'sort_order',
        'settings',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'enabled' => 'boolean',
        'test_mode' => 'boolean',
        'sort_order' => 'integer',
        'settings' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get only enabled payment gateways
     */
    public static function getEnabled()
    {
        return static::where('enabled', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get available payment methods for checkout
     */
    public static function getAvailableForCheckout(): array
    {
        return static::getEnabled()
            ->map(function ($gateway) {
                return [
                    'id' => $gateway->gateway,
                    'name' => $gateway->name,
                    'description' => $gateway->description,
                    'icon' => $gateway->icon,
                    'enabled' => true,
                ];
            })
            ->toArray();
    }

    /**
     * Check if gateway is configured (has credentials in .env)
     */
    public function isConfigured(): bool
    {
        switch ($this->gateway) {
            case 'stripe':
                $key = $this->test_mode ? 'stripe.stripe_test_sk' : 'stripe.stripe_live_sk';
                return !empty(config($key));
                
            case 'paypal':
                $mode = $this->test_mode ? 'sandbox' : 'live';
                return !empty(config("paypal.{$mode}.client_id")) 
                    && !empty(config("paypal.{$mode}.client_secret"));
                
            default:
                return false;
        }
    }

    /**
     * Get configuration status for display
     */
    public function getConfigStatusAttribute(): string
    {
        return $this->isConfigured() ? 'configured' : 'missing';
    }

    /**
     * Get the credentials for this gateway
     */
    public function getCredentials(): array
    {
        switch ($this->gateway) {
            case 'stripe':
                return [
                    'public_key' => $this->test_mode 
                        ? config('stripe.stripe_test_pk') 
                        : config('stripe.stripe_live_pk'),
                    'secret_key' => $this->test_mode 
                        ? config('stripe.stripe_test_sk') 
                        : config('stripe.stripe_live_sk'),
                ];
                
            case 'paypal':
                $mode = $this->test_mode ? 'sandbox' : 'live';
                return [
                    'client_id' => config("paypal.{$mode}.client_id"),
                    'client_secret' => config("paypal.{$mode}.client_secret"),
                    'mode' => $this->test_mode ? 'sandbox' : 'live',
                ];
                
            default:
                return [];
        }
    }
}
