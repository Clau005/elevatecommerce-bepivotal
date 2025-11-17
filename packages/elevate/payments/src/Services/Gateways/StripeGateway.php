<?php

namespace Elevate\Payments\Services\Gateways;

use Elevate\Payments\Contracts\PaymentGatewayInterface;
use Elevate\Payments\DTOs\PaymentRequest;
use Elevate\Payments\DTOs\PaymentResponse;
use Elevate\Payments\Models\PaymentGateway;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;
    private ?PaymentGateway $gatewayModel = null;
    
    public function __construct()
    {
        // Load Stripe gateway configuration from database
        $this->gatewayModel = PaymentGateway::where('name', 'Stripe')
            ->where('is_enabled', true)
            ->first();
        
        if ($this->gatewayModel) {
            $credentials = $this->gatewayModel->getActiveCredentials();
            $secretKey = $credentials['secret_key'] ?? null;
            
            if ($secretKey) {
                $this->stripe = new StripeClient($secretKey);
            }
        }
    }
    
    /**
     * Create a new payment
     */
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        if (!isset($this->stripe)) {
            Log::error('Stripe gateway not configured');
            return new PaymentResponse(
                success: false,
                error: 'Stripe gateway is not configured'
            );
        }
        
        try {
            Log::info('Creating Stripe payment intent', [
                'amount' => $request->amount,
                'currency' => $request->currency,
            ]);
            
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => $request->currency,
                'metadata' => $request->metadata,
                'automatic_payment_methods' => ['enabled' => true],
            ]);
            
            Log::info('Stripe payment intent created', [
                'payment_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);
            
            return new PaymentResponse(
                success: true,
                paymentId: $paymentIntent->id,
                status: $paymentIntent->status,
                data: [
                    'client_secret' => $paymentIntent->client_secret,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                ]
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment creation failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    /**
     * Capture an authorized payment
     */
    public function capturePayment(string $paymentId): PaymentResponse
    {
        if (!isset($this->stripe)) {
            return new PaymentResponse(
                success: false,
                error: 'Stripe gateway is not configured'
            );
        }
        
        try {
            Log::info('Capturing Stripe payment', ['payment_id' => $paymentId]);
            
            $paymentIntent = $this->stripe->paymentIntents->capture($paymentId);
            
            Log::info('Stripe payment captured', [
                'payment_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);
            
            return new PaymentResponse(
                success: true,
                paymentId: $paymentIntent->id,
                status: $paymentIntent->status,
                data: (array) $paymentIntent
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment capture failed', [
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
    public function refundPayment(string $paymentId, ?float $amount = null): PaymentResponse
    {
        if (!isset($this->stripe)) {
            return new PaymentResponse(
                success: false,
                error: 'Stripe gateway is not configured'
            );
        }
        
        try {
            $params = ['payment_intent' => $paymentId];
            
            if ($amount) {
                $params['amount'] = $amount * 100; // Convert to cents
            }
            
            Log::info('Creating Stripe refund', [
                'payment_id' => $paymentId,
                'amount' => $amount,
            ]);
            
            $refund = $this->stripe->refunds->create($params);
            
            Log::info('Stripe refund created', [
                'refund_id' => $refund->id,
                'status' => $refund->status,
            ]);
            
            return new PaymentResponse(
                success: true,
                paymentId: $refund->id,
                status: $refund->status,
                data: (array) $refund
            );
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed', [
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
     * Get the status of a payment
     */
    public function getPaymentStatus(string $paymentId): string
    {
        if (!isset($this->stripe)) {
            return 'unknown';
        }
        
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentId);
            return $paymentIntent->status;
        } catch (ApiErrorException $e) {
            Log::error('Failed to get Stripe payment status', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return 'unknown';
        }
    }
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhook(array $payload, string $signature): bool
    {
        if (!$this->gatewayModel) {
            return false;
        }
        
        $credentials = $this->gatewayModel->getActiveCredentials();
        $webhookSecret = $credentials['webhook_secret'] ?? null;
        
        if (!$webhookSecret) {
            Log::warning('Stripe webhook secret not configured');
            return false;
        }
        
        try {
            \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $signature,
                $webhookSecret
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Handle webhook payload
     */
    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['type'] ?? null;
        
        Log::info('Handling Stripe webhook', ['event_type' => $eventType]);
        
        match($eventType) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($payload),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($payload),
            'charge.refunded' => $this->handleRefund($payload),
            default => Log::info('Unhandled Stripe webhook event', ['type' => $eventType]),
        };
    }
    
    /**
     * Handle successful payment
     */
    protected function handlePaymentSucceeded(array $payload): void
    {
        $paymentIntent = $payload['data']['object'] ?? [];
        $paymentId = $paymentIntent['id'] ?? null;
        
        if (!$paymentId) {
            Log::warning('Stripe payment succeeded webhook missing payment ID');
            return;
        }
        
        Log::info('Stripe payment succeeded', ['payment_id' => $paymentId]);
        
        // Find the transaction
        $transaction = \Elevate\Payments\Models\Transaction::where('transaction_id', $paymentId)->first();
        
        if (!$transaction) {
            Log::warning('Transaction not found for Stripe payment', ['payment_id' => $paymentId]);
            return;
        }
        
        // Update transaction status
        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        Log::info('Transaction marked as completed', [
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
        ]);
        
        // Update order status to confirmed
        if ($transaction->order) {
            $transaction->order->update(['status' => 'confirmed']);
            
            Log::info('Order confirmed after payment', [
                'order_id' => $transaction->order->id,
                'order_reference' => $transaction->order->reference,
            ]);
            
            // Add timeline event
            $transaction->order->timelines()->create([
                'type' => 'payment_completed',
                'title' => 'Payment Completed',
                'content' => "Payment of {$transaction->formatted_amount} completed successfully via Stripe",
                'is_system_event' => true,
                'is_visible_to_customer' => true,
                'author_name' => 'System',
            ]);
        }
    }
    
    /**
     * Handle failed payment
     */
    protected function handlePaymentFailed(array $payload): void
    {
        $paymentIntent = $payload['data']['object'] ?? [];
        $paymentId = $paymentIntent['id'] ?? null;
        $errorMessage = $paymentIntent['last_payment_error']['message'] ?? 'Payment failed';
        
        if (!$paymentId) {
            Log::warning('Stripe payment failed webhook missing payment ID');
            return;
        }
        
        Log::warning('Stripe payment failed', [
            'payment_id' => $paymentId,
            'error' => $errorMessage,
        ]);
        
        // Find the transaction
        $transaction = \Elevate\Payments\Models\Transaction::where('transaction_id', $paymentId)->first();
        
        if (!$transaction) {
            Log::warning('Transaction not found for failed Stripe payment', ['payment_id' => $paymentId]);
            return;
        }
        
        // Update transaction status
        $transaction->update([
            'status' => 'failed',
            'notes' => $errorMessage,
        ]);
        
        Log::info('Transaction marked as failed', [
            'transaction_id' => $transaction->id,
            'order_id' => $transaction->order_id,
        ]);
        
        // Update order status to cancelled
        if ($transaction->order) {
            $transaction->order->update(['status' => 'cancelled']);
            
            Log::info('Order cancelled after payment failure', [
                'order_id' => $transaction->order->id,
                'order_reference' => $transaction->order->reference,
            ]);
            
            // Add timeline event
            $transaction->order->timelines()->create([
                'type' => 'payment_failed',
                'title' => 'Payment Failed',
                'content' => "Payment failed: {$errorMessage}",
                'is_system_event' => true,
                'is_visible_to_customer' => false,
                'author_name' => 'System',
            ]);
        }
    }
    
    /**
     * Handle refund
     */
    protected function handleRefund(array $payload): void
    {
        $charge = $payload['data']['object'] ?? [];
        $chargeId = $charge['id'] ?? null;
        
        Log::info('Stripe refund processed', ['charge_id' => $chargeId]);
        
        // Update transaction status in database
        // This will be handled by the webhook controller
    }
}
