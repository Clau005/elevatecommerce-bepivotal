Introduction
Modern e-commerce platforms require flexibility in payment processing. Supporting multiple payment gateways increases conversion rates by offering customers their preferred payment methods, provides redundancy when one gateway fails, and enables regional payment optimization.

Integrating multiple payment gateways while maintaining clean, maintainable code requires proper abstraction. Laravel 12's service container and interface-driven design make implementing a unified payment layer straightforward, allowing seamless switching between PayPal, Stripe, and other providers.

In this comprehensive guide, we'll build a flexible multi-gateway payment system for Laravel 12, covering PayPal integration, payment abstraction patterns, gateway switching, webhook handling, and best practices for scalable payment processing.

Payment Gateway Architecture
Multi-Gateway Design Pattern
┌─────────────────┐
│ Payment Facade  │
└────────┬────────┘
         │
    ┌────▼────┐
    │ Gateway │
    │ Manager │
    └────┬────┘
         │
    ┌────▼────────────────┐
    │ Gateway Interface   │
    └──┬──────┬──────┬───┘
       │      │      │
   ┌───▼──┐ ┌▼────┐ ┌▼──────┐
   │Stripe│ │PayPal│ │Authorize│
   │      │ │      │ │.Net    │
   └──────┘ └──────┘ └────────┘
Benefits of Abstraction
Unified Interface – Consistent API across all gateways
Easy Switching – Change providers without code changes
Testability – Mock payment gateways in tests
Maintainability – Gateway-specific code isolated
Extensibility – Add new gateways easily
Database Schema for Multi-Gateway
Payment Tables Migration
Schema::create('payment_gateways', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // stripe, paypal, authorize
    $table->string('display_name');
    $table->boolean('is_active')->default(true);
    $table->json('configuration');
    $table->integer('priority')->default(0);
    $table->timestamps();
    
    $table->index('is_active');
});

Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained();
    $table->string('gateway'); // stripe, paypal
    $table->string('transaction_id')->unique();
    $table->string('payment_method'); // card, paypal, bank_transfer
    $table->decimal('amount', 10, 2);
    $table->string('currency', 3)->default('usd');
    $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded']);
    $table->json('gateway_response')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    
    $table->index(['order_id', 'status']);
    $table->index('transaction_id');
});

Schema::create('refunds', function (Blueprint $table) {
    $table->id();
    $table->foreignId('transaction_id')->constrained();
    $table->string('refund_id')->unique();
    $table->decimal('amount', 10, 2);
    $table->string('reason')->nullable();
    $table->enum('status', ['pending', 'completed', 'failed']);
    $table->json('gateway_response')->nullable();
    $table->timestamps();
});
Payment Gateway Interface
1. Gateway Contract
namespace App\Contracts;

use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;

interface PaymentGatewayInterface
{
    public function createPayment(PaymentRequest $request): PaymentResponse;
    
    public function capturePayment(string $paymentId): PaymentResponse;
    
    public function refundPayment(string $paymentId, ?float $amount = null): PaymentResponse;
    
    public function getPaymentStatus(string $paymentId): string;
    
    public function verifyWebhook(array $payload, string $signature): bool;
    
    public function handleWebhook(array $payload): void;
}
2. Data Transfer Objects
namespace App\DTOs;

class PaymentRequest
{
    public function __construct(
        public float $amount,
        public string $currency,
        public array $metadata = [],
        public ?string $returnUrl = null,
        public ?string $cancelUrl = null,
    ) {}
}

class PaymentResponse
{
    public function __construct(
        public bool $success,
        public ?string $paymentId = null,
        public ?string $redirectUrl = null,
        public ?string $status = null,
        public ?array $data = null,
        public ?string $error = null,
    ) {}
}
PayPal Integration
1. Install PayPal SDK
composer require paypal/paypal-checkout-sdk
2. PayPal Gateway Implementation
namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\{PaymentRequest, PaymentResponse};
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\{SandboxEnvironment, ProductionEnvironment};
use PayPalCheckoutSdk\Orders\{OrdersCreateRequest, OrdersCaptureRequest, OrdersGetRequest};
use PayPalHttp\HttpException;

class PayPalGateway implements PaymentGatewayInterface
{
    private PayPalHttpClient $client;
    
    public function __construct()
    {
        $environment = config('services.paypal.mode') === 'live'
            ? new ProductionEnvironment(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
            : new SandboxEnvironment(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            );
        
        $this->client = new PayPalHttpClient($environment);
    }
    
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        $orderRequest = new OrdersCreateRequest();
        $orderRequest->prefer('return=representation');
        $orderRequest->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($request->currency),
                    'value' => number_format($request->amount, 2, '.', ''),
                ],
                'custom_id' => $request->metadata['order_id'] ?? null,
            ]],
            'application_context' => [
                'return_url' => $request->returnUrl,
                'cancel_url' => $request->cancelUrl,
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
            ],
        ];
        
        try {
            $response = $this->client->execute($orderRequest);
            
            // Extract approval URL
            $approvalUrl = collect($response->result->links)
                ->firstWhere('rel', 'approve')
                ->href ?? null;
            
            return new PaymentResponse(
                success: true,
                paymentId: $response->result->id,
                redirectUrl: $approvalUrl,
                status: $response->result->status,
                data: (array) $response->result
            );
        } catch (HttpException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function capturePayment(string $paymentId): PaymentResponse
    {
        $request = new OrdersCaptureRequest($paymentId);
        
        try {
            $response = $this->client->execute($request);
            
            return new PaymentResponse(
                success: true,
                paymentId: $response->result->id,
                status: $response->result->status,
                data: (array) $response->result
            );
        } catch (HttpException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function refundPayment(string $paymentId, ?float $amount = null): PaymentResponse
    {
        // First, get the capture ID from the order
        $orderRequest = new OrdersGetRequest($paymentId);
        
        try {
            $orderResponse = $this->client->execute($orderRequest);
            $captureId = $orderResponse->result->purchase_units[0]->payments->captures[0]->id;
            
            // Create refund request
            $refundRequest = new \PayPalCheckoutSdk\Payments\CapturesRefundRequest($captureId);
            
            if ($amount) {
                $refundRequest->body = [
                    'amount' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency_code' => 'USD',
                    ],
                ];
            }
            
            $refundResponse = $this->client->execute($refundRequest);
            
            return new PaymentResponse(
                success: true,
                paymentId: $refundResponse->result->id,
                status: $refundResponse->result->status,
                data: (array) $refundResponse->result
            );
        } catch (HttpException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function getPaymentStatus(string $paymentId): string
    {
        $request = new OrdersGetRequest($paymentId);
        
        try {
            $response = $this->client->execute($request);
            return $response->result->status;
        } catch (HttpException $e) {
            return 'unknown';
        }
    }
    
    public function verifyWebhook(array $payload, string $signature): bool
    {
        // Implement PayPal webhook signature verification
        return true; // Simplified for example
    }
    
    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['event_type'] ?? null;
        
        match($eventType) {
            'CHECKOUT.ORDER.APPROVED' => $this->handleOrderApproved($payload),
            'PAYMENT.CAPTURE.COMPLETED' => $this->handlePaymentCompleted($payload),
            'PAYMENT.CAPTURE.REFUNDED' => $this->handlePaymentRefunded($payload),
            default => null,
        };
    }
    
    protected function handleOrderApproved(array $payload): void
    {
        // Handle order approval
    }
    
    protected function handlePaymentCompleted(array $payload): void
    {
        // Handle payment completion
    }
    
    protected function handlePaymentRefunded(array $payload): void
    {
        // Handle refund
    }
}
Stripe Gateway Implementation
namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\{PaymentRequest, PaymentResponse};
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;
    
    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }
    
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $request->amount * 100,
                'currency' => $request->currency,
                'metadata' => $request->metadata,
                'automatic_payment_methods' => ['enabled' => true],
            ]);
            
            return new PaymentResponse(
                success: true,
                paymentId: $paymentIntent->id,
                status: $paymentIntent->status,
                data: ['client_secret' => $paymentIntent->client_secret]
            );
        } catch (ApiErrorException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function capturePayment(string $paymentId): PaymentResponse
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->capture($paymentId);
            
            return new PaymentResponse(
                success: true,
                paymentId: $paymentIntent->id,
                status: $paymentIntent->status
            );
        } catch (ApiErrorException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function refundPayment(string $paymentId, ?float $amount = null): PaymentResponse
    {
        try {
            $params = ['payment_intent' => $paymentId];
            
            if ($amount) {
                $params['amount'] = $amount * 100;
            }
            
            $refund = $this->stripe->refunds->create($params);
            
            return new PaymentResponse(
                success: true,
                paymentId: $refund->id,
                status: $refund->status
            );
        } catch (ApiErrorException $e) {
            return new PaymentResponse(
                success: false,
                error: $e->getMessage()
            );
        }
    }
    
    public function getPaymentStatus(string $paymentId): string
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentId);
            return $paymentIntent->status;
        } catch (ApiErrorException $e) {
            return 'unknown';
        }
    }
    
    public function verifyWebhook(array $payload, string $signature): bool
    {
        try {
            \Stripe\Webhook::constructEvent(
                json_encode($payload),
                $signature,
                config('services.stripe.webhook_secret')
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['type'] ?? null;
        
        match($eventType) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($payload),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($payload),
            'charge.refunded' => $this->handleRefund($payload),
            default => null,
        };
    }
    
    protected function handlePaymentSucceeded(array $payload): void
    {
        // Handle payment success
    }
    
    protected function handlePaymentFailed(array $payload): void
    {
        // Handle payment failure
    }
    
    protected function handleRefund(array $payload): void
    {
        // Handle refund
    }
}
Gateway Manager
Payment Gateway Factory
namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Services\PaymentGateways\{StripeGateway, PayPalGateway};

class PaymentGatewayManager
{
    private array $gateways = [];
    
    public function __construct()
    {
        $this->registerGateways();
    }
    
    protected function registerGateways(): void
    {
        $this->gateways = [
            'stripe' => app(StripeGateway::class),
            'paypal' => app(PayPalGateway::class),
        ];
    }
    
    public function gateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \InvalidArgumentException("Gateway {$name} not supported");
        }
        
        return $this->gateways[$name];
    }
    
    public function getActiveGateways(): array
    {
        return array_filter($this->gateways, function ($gateway, $name) {
            return config("services.{$name}.enabled", false);
        }, ARRAY_FILTER_USE_BOTH);
    }
    
    public function getDefaultGateway(): PaymentGatewayInterface
    {
        $default = config('payment.default_gateway', 'stripe');
        return $this->gateway($default);
    }
}
Payment Service with Multi-Gateway
namespace App\Services;

use App\Models\{Order, Transaction};
use App\DTOs\PaymentRequest;

class PaymentService
{
    public function __construct(
        private PaymentGatewayManager $gatewayManager
    ) {}
    
    public function initiatePayment(Order $order, string $gateway = null): array
    {
        $gateway = $gateway ?? config('payment.default_gateway');
        $paymentGateway = $this->gatewayManager->gateway($gateway);
        
        $paymentRequest = new PaymentRequest(
            amount: $order->total,
            currency: $order->currency ?? 'usd',
            metadata: [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            returnUrl: route('payment.success', $order),
            cancelUrl: route('payment.cancel', $order)
        );
        
        $response = $paymentGateway->createPayment($paymentRequest);
        
        if ($response->success) {
            // Create transaction record
            $transaction = Transaction::create([
                'order_id' => $order->id,
                'gateway' => $gateway,
                'transaction_id' => $response->paymentId,
                'payment_method' => $gateway,
                'amount' => $order->total,
                'currency' => $order->currency ?? 'usd',
                'status' => 'pending',
                'gateway_response' => $response->data,
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $transaction->id,
                'payment_id' => $response->paymentId,
                'redirect_url' => $response->redirectUrl,
                'client_secret' => $response->data['client_secret'] ?? null,
            ];
        }
        
        return [
            'success' => false,
            'error' => $response->error,
        ];
    }
    
    public function capturePayment(Transaction $transaction): bool
    {
        $gateway = $this->gatewayManager->gateway($transaction->gateway);
        $response = $gateway->capturePayment($transaction->transaction_id);
        
        if ($response->success) {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            $transaction->order->update(['status' => 'processing']);
            
            return true;
        }
        
        $transaction->update(['status' => 'failed']);
        return false;
    }
    
    public function refundPayment(Transaction $transaction, ?float $amount = null): bool
    {
        $gateway = $this->gatewayManager->gateway($transaction->gateway);
        $response = $gateway->refundPayment($transaction->transaction_id, $amount);
        
        if ($response->success) {
            $transaction->refunds()->create([
                'refund_id' => $response->paymentId,
                'amount' => $amount ?? $transaction->amount,
                'status' => 'completed',
                'gateway_response' => $response->data,
            ]);
            
            $transaction->update(['status' => 'refunded']);
            $transaction->order->update(['status' => 'refunded']);
            
            return true;
        }
        
        return false;
    }
}
Checkout Controller
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}
    
    public function selectPaymentMethod(Order $order)
    {
        $gateways = config('payment.enabled_gateways');
        
        return view('checkout.payment-method', compact('order', 'gateways'));
    }
    
    public function processPayment(Request $request, Order $order)
    {
        $request->validate([
            'gateway' => 'required|in:stripe,paypal',
        ]);
        
        $result = $this->paymentService->initiatePayment(
            $order,
            $request->gateway
        );
        
        if ($result['success']) {
            if ($result['redirect_url']) {
                // PayPal redirect flow
                return redirect($result['redirect_url']);
            }
            
            // Stripe Elements flow
            return view('checkout.payment', [
                'order' => $order,
                'clientSecret' => $result['client_secret'],
                'gateway' => $request->gateway,
            ]);
        }
        
        return back()->withErrors(['payment' => $result['error']]);
    }
    
    public function paymentReturn(Request $request, Order $order)
    {
        $transaction = $order->transactions()
            ->where('status', 'pending')
            ->latest()
            ->first();
        
        if (!$transaction) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Transaction not found');
        }
        
        // For PayPal, capture the payment
        if ($transaction->gateway === 'paypal' && $request->has('token')) {
            $captured = $this->paymentService->capturePayment($transaction);
            
            if ($captured) {
                return redirect()->route('orders.show', $order)
                    ->with('success', 'Payment completed successfully');
            }
        }
        
        return redirect()->route('orders.show', $order)
            ->with('error', 'Payment capture failed');
    }
    
    public function paymentCancel(Order $order)
    {
        return redirect()->route('checkout.payment-method', $order)
            ->with('warning', 'Payment was cancelled');
    }
}
Unified Webhook Handler
namespace App\Http\Controllers;

use App\Services\PaymentGatewayManager;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayManager $gatewayManager
    ) {}
    
    public function handleStripe(Request $request)
    {
        $gateway = $this->gatewayManager->gateway('stripe');
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature');
        
        if (!$gateway->verifyWebhook($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        $gateway->handleWebhook($payload);
        
        return response()->json(['status' => 'success']);
    }
    
    public function handlePayPal(Request $request)
    {
        $gateway = $this->gatewayManager->gateway('paypal');
        $payload = $request->all();
        $signature = $request->header('PAYPAL-TRANSMISSION-SIG');
        
        if (!$gateway->verifyWebhook($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        $gateway->handleWebhook($payload);
        
        return response()->json(['status' => 'success']);
    }
}
Configuration
payment.php Config File
return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),
    
    'enabled_gateways' => [
        'stripe' => env('PAYMENT_STRIPE_ENABLED', true),
        'paypal' => env('PAYMENT_PAYPAL_ENABLED', true),
    ],
    
    'currency' => env('PAYMENT_CURRENCY', 'usd'),
    
    'test_mode' => env('PAYMENT_TEST_MODE', true),
];
services.php Configuration
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'enabled' => env('PAYMENT_STRIPE_ENABLED', true),
],

'paypal' => [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'secret' => env('PAYPAL_SECRET'),
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    'enabled' => env('PAYMENT_PAYPAL_ENABLED', true),
],
Testing Multi-Gateway Payments
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Services\PaymentService;

class MultiGatewayPaymentTest extends TestCase
{
    /** @test */
    public function it_processes_stripe_payment()
    {
        $order = Order::factory()->create();
        $paymentService = app(PaymentService::class);
        
        $result = $paymentService->initiatePayment($order, 'stripe');
        
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['client_secret']);
    }
    
    /** @test */
    public function it_processes_paypal_payment()
    {
        $order = Order::factory()->create();
        $paymentService = app(PaymentService::class);
        
        $result = $paymentService->initiatePayment($order, 'paypal');
        
        $this->assertTrue($result['success']);
        $this->assertNotNull($result['redirect_url']);
    }
    
    /** @test */
    public function it_switches_between_gateways()
    {
        $order = Order::factory()->create();
        $paymentService = app(PaymentService::class);
        
        // Try Stripe first
        $stripeResult = $paymentService->initiatePayment($order, 'stripe');
        $this->assertEquals('stripe', $order->transactions()->first()->gateway);
        
        // Switch to PayPal
        $paypalResult = $paymentService->initiatePayment($order, 'paypal');
        $this->assertEquals('paypal', $order->transactions()->latest()->first()->gateway);
    }
}
Best Practices
1. Gateway Fallback Strategy
public function initiatePaymentWithFallback(Order $order): array
{
    $gateways = ['stripe', 'paypal', 'authorize'];
    
    foreach ($gateways as $gateway) {
        try {
            $result = $this->initiatePayment($order, $gateway);
            
            if ($result['success']) {
                return $result;
            }
        } catch (\Exception $e) {
            Log::warning("Gateway {$gateway} failed", ['error' => $e->getMessage()]);
            continue;
        }
    }
    
    throw new \Exception('All payment gateways failed');
}
2. Currency Conversion
public function convertCurrency(float $amount, string $from, string $to): float
{
    // Use exchange rate API
    $rate = Cache::remember("exchange_rate_{$from}_{$to}", 3600, function () use ($from, $to) {
        return Http::get("https://api.exchangerate.host/convert", [
            'from' => $from,
            'to' => $to,
        ])->json('result');
    });
    
    return $amount * $rate;
}
3. Gateway Health Checks
public function checkGatewayHealth(string $gateway): bool
{
    try {
        $paymentGateway = $this->gatewayManager->gateway($gateway);
        $status = $paymentGateway->getPaymentStatus('test_payment_id');
        return true;
    } catch (\Exception $e) {
        return false;
    }
}
Conclusion
Implementing multi-gateway payment processing in Laravel 12 with proper abstraction enables flexible, maintainable e-commerce systems. By using interfaces, factory patterns, and unified service layers, you can seamlessly integrate PayPal, Stripe, and other providers while maintaining clean, testable code that adapts to changing business requirements.

Key takeaways:

Use interface-driven design for gateway abstraction
Implement factory pattern for gateway management
Create unified payment service layer
Handle webhooks for each gateway separately
Support gateway fallback strategies
Test payment flows thoroughly
Monitor gateway health and performance
