<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Elevate\Payments\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Stripe keys are configured
        $hasStripeKeys = env('STRIPE_TEST_PUBLISHABLE_KEY') && env('STRIPE_TEST_SECRET_KEY');
        
        if (!$hasStripeKeys) {
            $this->command->warn('⚠️  Stripe credentials not found in .env');
            $this->command->info('Add these to your .env file:');
            $this->command->line('STRIPE_TEST_MODE=true');
            $this->command->line('STRIPE_TEST_PUBLISHABLE_KEY=pk_test_...');
            $this->command->line('STRIPE_TEST_SECRET_KEY=sk_test_...');
            $this->command->line('STRIPE_TEST_WEBHOOK_SECRET=whsec_...');
            $this->command->newLine();
            $this->command->info('Creating gateway with placeholder credentials...');
        }
        
        // Create Stripe gateway
        PaymentGateway::updateOrCreate(
            ['name' => 'Stripe'],
            [
                'display_name' => 'Credit Card (Stripe)',
                'driver' => 'stripe',
                'is_enabled' => $hasStripeKeys, // Only enable if keys are present
                'test_mode' => true,
                'test_credentials' => $hasStripeKeys ? [
                    'publishable_key' => env('STRIPE_TEST_PUBLISHABLE_KEY'),
                    'secret_key' => env('STRIPE_TEST_SECRET_KEY'),
                    'webhook_secret' => env('STRIPE_TEST_WEBHOOK_SECRET', ''),
                ] : [
                    'publishable_key' => 'pk_test_REPLACE_WITH_YOUR_KEY',
                    'secret_key' => 'sk_test_REPLACE_WITH_YOUR_KEY',
                    'webhook_secret' => 'whsec_REPLACE_WITH_YOUR_SECRET',
                ],
                'credentials' => [
                    'publishable_key' => env('STRIPE_LIVE_PUBLISHABLE_KEY', ''),
                    'secret_key' => env('STRIPE_LIVE_SECRET_KEY', ''),
                    'webhook_secret' => env('STRIPE_LIVE_WEBHOOK_SECRET', ''),
                ],
                'settings' => [
                    'payment_methods' => ['card'],
                    'capture_method' => 'automatic',
                ],
                'sort_order' => 1,
            ]
        );

        if ($hasStripeKeys) {
            $this->command->info('✅ Stripe payment gateway created and ENABLED');
        } else {
            $this->command->warn('⚠️  Stripe payment gateway created but DISABLED');
            $this->command->info('   Add your Stripe keys to .env and run this seeder again');
        }
        
        // You can add more gateways here in the future
        // Example: PayPal, Authorize.Net, etc.
    }
}
