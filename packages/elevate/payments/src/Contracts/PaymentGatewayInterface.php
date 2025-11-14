<?php

namespace Elevate\Payments\Contracts;

use Elevate\Payments\DTOs\PaymentRequest;
use Elevate\Payments\DTOs\PaymentResponse;

interface PaymentGatewayInterface
{
    /**
     * Create a new payment
     */
    public function createPayment(PaymentRequest $request): PaymentResponse;
    
    /**
     * Capture an authorized payment
     */
    public function capturePayment(string $paymentId): PaymentResponse;
    
    /**
     * Refund a payment
     */
    public function refundPayment(string $paymentId, ?float $amount = null): PaymentResponse;
    
    /**
     * Get the status of a payment
     */
    public function getPaymentStatus(string $paymentId): string;
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhook(array $payload, string $signature): bool;
    
    /**
     * Handle webhook payload
     */
    public function handleWebhook(array $payload): void;
}
