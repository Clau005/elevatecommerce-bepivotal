<?php

namespace Elevate\Payments\Services;

use Elevate\Payments\Models\PaymentGateway;
use Omnipay\Omnipay;
use Omnipay\Common\GatewayInterface;

class PaymentService
{
    /**
     * Get all enabled payment gateways
     */
    public function getEnabledGateways()
    {
        return PaymentGateway::getEnabled();
    }

    /**
     * Create an Omnipay gateway instance
     */
    public function createGateway(PaymentGateway $config): GatewayInterface
    {
        $gateway = Omnipay::create($this->getOmnipayDriver($config->driver));
        
        // Get appropriate credentials based on test mode
        $credentials = $config->getActiveCredentials();
        
        // Add test mode flag
        $credentials['testMode'] = $config->test_mode;
        
        $gateway->initialize($credentials);
        
        return $gateway;
    }

    /**
     * Process a payment
     */
    public function charge(int $gatewayId, float $amount, array $paymentData)
    {
        $config = PaymentGateway::findOrFail($gatewayId);
        
        if (!$config->is_enabled) {
            throw new \Exception('Payment gateway is disabled');
        }

        $gateway = $this->createGateway($config);

        $response = $gateway->purchase([
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => $paymentData['currency'] ?? 'GBP',
            'card' => $paymentData['card'] ?? null,
            'token' => $paymentData['token'] ?? null,
            'returnUrl' => $paymentData['returnUrl'] ?? route('checkout.complete'),
            'cancelUrl' => $paymentData['cancelUrl'] ?? route('checkout.index'),
        ])->send();

        return [
            'success' => $response->isSuccessful(),
            'redirect' => $response->isRedirect() ? $response->getRedirectUrl() : null,
            'message' => $response->getMessage(),
            'reference' => $response->getTransactionReference(),
            'data' => $response->getData(),
        ];
    }

    /**
     * Map our driver names to Omnipay driver names
     */
    protected function getOmnipayDriver(string $driver): string
    {
        return match($driver) {
            'stripe' => 'Stripe',
            'paypal' => 'PayPal_Express',
            'worldpay' => 'Worldpay',
            default => ucfirst($driver)
        };
    }
}
