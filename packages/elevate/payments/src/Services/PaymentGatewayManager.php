<?php

namespace Elevate\Payments\Services;

use Elevate\Payments\Contracts\PaymentGatewayInterface;
use Elevate\Payments\Services\Gateways\StripeGateway;
use Elevate\Payments\Models\PaymentGateway as PaymentGatewayModel;

class PaymentGatewayManager
{
    private array $gateways = [];
    
    public function __construct()
    {
        $this->registerGateways();
    }
    
    /**
     * Register all available payment gateways
     */
    protected function registerGateways(): void
    {
        $this->gateways = [
            'stripe' => app(StripeGateway::class),
            // Add more gateways here as they're implemented
            // 'paypal' => app(PayPalGateway::class),
        ];
    }
    
    /**
     * Get a specific gateway by name
     */
    public function gateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \InvalidArgumentException("Gateway {$name} is not supported");
        }
        
        return $this->gateways[$name];
    }
    
    /**
     * Get gateway by database model
     */
    public function gatewayFromModel(PaymentGatewayModel $model): PaymentGatewayInterface
    {
        $gatewayName = strtolower($model->name);
        return $this->gateway($gatewayName);
    }
    
    /**
     * Get all active gateways from database
     */
    public function getActiveGateways(): array
    {
        $activeModels = PaymentGatewayModel::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
        
        $activeGateways = [];
        foreach ($activeModels as $model) {
            $gatewayName = strtolower($model->name);
            if (isset($this->gateways[$gatewayName])) {
                $activeGateways[$gatewayName] = [
                    'gateway' => $this->gateways[$gatewayName],
                    'model' => $model,
                ];
            }
        }
        
        return $activeGateways;
    }
    
    /**
     * Get the default gateway
     */
    public function getDefaultGateway(): ?PaymentGatewayInterface
    {
        $defaultModel = PaymentGatewayModel::where('is_enabled', true)
            ->orderBy('sort_order')
            ->first();
        
        if (!$defaultModel) {
            return null;
        }
        
        return $this->gatewayFromModel($defaultModel);
    }
    
    /**
     * Check if a gateway is registered
     */
    public function hasGateway(string $name): bool
    {
        return isset($this->gateways[$name]);
    }
}
