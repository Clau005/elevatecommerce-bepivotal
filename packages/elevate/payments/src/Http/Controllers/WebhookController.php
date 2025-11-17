<?php

namespace Elevate\Payments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Elevate\Payments\Services\PaymentGatewayManager;
use Elevate\Payments\Models\PaymentGateway;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayManager $gatewayManager
    ) {}
    
    /**
     * Handle Stripe webhooks
     */
    public function handleStripe(Request $request)
    {
        Log::info('Received Stripe webhook');
        
        $gatewayModel = PaymentGateway::where('name', 'Stripe')
            ->where('is_enabled', true)
            ->first();
        
        if (!$gatewayModel) {
            Log::error('Stripe gateway not found or disabled');
            return response()->json(['error' => 'Gateway not configured'], 400);
        }
        
        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            $payload = $request->all();
            $signature = $request->header('Stripe-Signature');
            
            if (!$gateway->verifyWebhook($payload, $signature)) {
                Log::warning('Stripe webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
            
            $gateway->handleWebhook($payload);
            
            Log::info('Stripe webhook processed successfully');
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
    
    /**
     * Handle PayPal webhooks
     */
    public function handlePayPal(Request $request)
    {
        Log::info('Received PayPal webhook');
        
        $gatewayModel = PaymentGateway::where('name', 'PayPal')
            ->where('is_enabled', true)
            ->first();
        
        if (!$gatewayModel) {
            Log::error('PayPal gateway not found or disabled');
            return response()->json(['error' => 'Gateway not configured'], 400);
        }
        
        try {
            $gateway = $this->gatewayManager->gatewayFromModel($gatewayModel);
            $payload = $request->all();
            $signature = $request->header('PAYPAL-TRANSMISSION-SIG');
            
            if (!$gateway->verifyWebhook($payload, $signature)) {
                Log::warning('PayPal webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
            
            $gateway->handleWebhook($payload);
            
            Log::info('PayPal webhook processed successfully');
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('PayPal webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
    
    /**
     * Generic webhook handler that routes to the correct gateway
     */
    public function handle(Request $request, string $gateway)
    {
        Log::info('Received webhook', ['gateway' => $gateway]);
        
        $gatewayModel = PaymentGateway::where('name', ucfirst($gateway))
            ->where('is_enabled', true)
            ->first();
        
        if (!$gatewayModel) {
            Log::error('Gateway not found or disabled', ['gateway' => $gateway]);
            return response()->json(['error' => 'Gateway not configured'], 400);
        }
        
        try {
            $gatewayInstance = $this->gatewayManager->gatewayFromModel($gatewayModel);
            $payload = $request->all();
            
            // Get signature from appropriate header based on gateway
            $signature = match(strtolower($gateway)) {
                'stripe' => $request->header('Stripe-Signature'),
                'paypal' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                default => $request->header('X-Signature'),
            };
            
            if (!$gatewayInstance->verifyWebhook($payload, $signature ?? '')) {
                Log::warning('Webhook signature verification failed', ['gateway' => $gateway]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
            
            $gatewayInstance->handleWebhook($payload);
            
            Log::info('Webhook processed successfully', ['gateway' => $gateway]);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}
