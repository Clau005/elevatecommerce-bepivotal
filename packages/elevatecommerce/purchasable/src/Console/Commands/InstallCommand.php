<?php

namespace ElevateCommerce\Purchasable\Console\Commands;

use ElevateCommerce\Purchasable\Models\PaymentGateway;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'purchasable:install';

    /**
     * The console command description.
     */
    protected $description = 'Install the Purchasable package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Purchasable Package...');

        // Run migrations
        $this->info('Running migrations...');
        $this->call('migrate');

        // Seed payment gateways
        $this->info('Seeding payment gateways...');
        $this->seedPaymentGateways();
        $this->info('✓ Payment gateways seeded (Stripe & PayPal)');

        // Publish config
        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--tag' => 'purchasable-config',
            '--force' => true,
        ]);

        $this->newLine();
        $this->info('✓ Purchasable package installed successfully!');
        $this->newLine();
        
        $this->comment('Next steps:');
        $this->line('1. Configure payment credentials in your .env file:');
        $this->line('   - STRIPE_TEST_PK, STRIPE_TEST_SK');
        $this->line('   - PAYPAL_SANDBOX_CLIENT_ID, PAYPAL_SANDBOX_CLIENT_SECRET');
        $this->line('2. Enable/disable payment gateways in the admin panel');
        $this->line('3. Toggle test mode for each gateway as needed');

        return self::SUCCESS;
    }

    /**
     * Seed payment gateways
     */
    protected function seedPaymentGateways(): void
    {
        $gateways = [
            [
                'gateway' => 'stripe',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay securely with your credit or debit card via Stripe',
                'icon' => 'fas fa-credit-card',
                'enabled' => true,
                'test_mode' => true,
                'sort_order' => 1,
                'settings' => [
                    'refunds_enabled' => true,
                    'webhooks_enabled' => false,
                    'capture_method' => 'automatic',
                ],
                'metadata' => [
                    'supports_refunds' => true,
                    'supports_webhooks' => true,
                    'supports_subscriptions' => true,
                ],
            ],
            [
                'gateway' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pay securely with your PayPal account',
                'icon' => 'fab fa-paypal',
                'enabled' => true,
                'test_mode' => true,
                'sort_order' => 2,
                'settings' => [
                    'refunds_enabled' => true,
                    'express_checkout' => true,
                ],
                'metadata' => [
                    'supports_refunds' => true,
                    'supports_webhooks' => true,
                    'supports_subscriptions' => true,
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['gateway' => $gateway['gateway']],
                $gateway
            );
        }
    }
}
