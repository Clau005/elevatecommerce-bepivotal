<?php

namespace Elevate\Payments\Services;

use Elevate\Payments\Models\PaymentGateway;
use Elevate\Payments\Models\Transaction;
use Elevate\Payments\DTOs\{PaymentRequest, PaymentResponse};
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private PaymentGatewayManager $gatewayManager
    ) {}

    /**
     * Get all enabled payment gateways
     */
    public function getEnabledGateways()
    {
        return PaymentGateway::getEnabled();
    }

    /**
     * Initiate a payment using a specific gateway
     */
    public function initiatePayment(
        PaymentGateway $gatewayModel,
        float $amount,
        string $currency,
        array $metadata = [],
        ?string $returnUrl = null,
        ?string $cancelUrl = null
    ): PaymentResponse {
        Log::info('Initiating payment', [
            'gateway' => $gatewayModel->name,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        if (!$gatewayModel->is_enabled) {
            Log::warning('Attempted to use disabled gateway', ['gateway' => $gatewayModel->name]);
            return new PaymentResponse(
                success: false,
                error: 'Payment gateway is disabled'
            );
        }

        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            
            $paymentRequest = new PaymentRequest(
                amount: $amount,
                currency: $currency,
                metadata: $metadata,
                returnUrl: $returnUrl,
                cancelUrl: $cancelUrl
            );
            
            $response = $gateway->createPayment($paymentRequest);
            
            Log::info('Payment initiation result', [
                'success' => $response->success,
                'payment_id' => $response->paymentId,
            ]);
            
            // Create transaction record if payment was successful
            if ($response->success && $response->paymentId) {
                $orderId = $metadata['order_id'] ?? null;
                
                if ($orderId) {
                    Transaction::create([
                        'order_id' => $orderId,
                        'gateway' => strtolower($gatewayModel->name),
                        'transaction_id' => $response->paymentId,
                        'payment_method' => $metadata['payment_method'] ?? strtolower($gatewayModel->name),
                        'amount' => $amount, // Amount is already in correct format (pounds)
                        'currency' => $currency,
                        'status' => 'pending',
                        'gateway_response' => $response->data,
                        'metadata' => $metadata,
                    ]);
                    
                    Log::info('Transaction record created', [
                        'payment_id' => $response->paymentId,
                        'order_id' => $orderId,
                    ]);
                }
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'gateway' => $gatewayModel->name,
                'error' => $e->getMessage(),
            ]);
            
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }

    /**
     * Legacy charge method for backward compatibility
     * @deprecated Use initiatePayment instead
     */
    public function charge(
        PaymentGateway $gateway,
        float $amount,
        string $currency,
        string $description,
        array $metadata = []
    ): PaymentResponse {
        return $this->initiatePayment(
            gatewayModel: $gateway,
            amount: $amount,
            currency: $currency,
            metadata: array_merge($metadata, ['description' => $description])
        );
    }

    /**
     * Capture an authorized payment
     */
    public function capturePayment(PaymentGateway $gatewayModel, string $paymentId): PaymentResponse
    {
        Log::info('Capturing payment', [
            'gateway' => $gatewayModel->name,
            'payment_id' => $paymentId,
        ]);

        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            $response = $gateway->capturePayment($paymentId);
            
            Log::info('Payment capture result', [
                'success' => $response->success,
                'payment_id' => $paymentId,
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Payment capture failed', [
                'gateway' => $gatewayModel->name,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(
        PaymentGateway $gatewayModel,
        string $paymentId,
        ?float $amount = null
    ): PaymentResponse {
        Log::info('Refunding payment', [
            'gateway' => $gatewayModel->name,
            'payment_id' => $paymentId,
            'amount' => $amount,
        ]);

        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            $response = $gateway->refundPayment($paymentId, $amount);
            
            Log::info('Payment refund result', [
                'success' => $response->success,
                'payment_id' => $paymentId,
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Payment refund failed', [
                'gateway' => $gatewayModel->name,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(PaymentGateway $gatewayModel, string $paymentId): string
    {
        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            return $gateway->getPaymentStatus($paymentId);
        } catch (\Exception $e) {
            Log::error('Failed to get payment status', [
                'gateway' => $gatewayModel->name,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return 'unknown';
        }
    }

    /**
     * Initiate payment with fallback strategy
     */
    public function initiatePaymentWithFallback(
        float $amount,
        string $currency,
        array $metadata = [],
        ?string $returnUrl = null,
        ?string $cancelUrl = null
    ): PaymentResponse {
        $gateways = PaymentGateway::where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($gateways as $gateway) {
            try {
                $result = $this->initiatePayment(
                    $gateway,
                    $amount,
                    $currency,
                    $metadata,
                    $returnUrl,
                    $cancelUrl
                );

                if ($result->success) {
                    return $result;
                }

                Log::warning("Gateway {$gateway->name} failed, trying next", [
                    'error' => $result->error
                ]);
            } catch (\Exception $e) {
                Log::warning("Gateway {$gateway->name} failed", [
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return new PaymentResponse(
            success: false,
            error: 'All payment gateways failed'
        );
    }
}
