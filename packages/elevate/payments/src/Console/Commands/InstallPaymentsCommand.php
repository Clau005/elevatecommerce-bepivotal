<?php

namespace Elevate\Payments\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Payments\Models\PaymentGateway;

class InstallPaymentsCommand extends Command
{
    protected $signature = 'payments:install';
    protected $description = 'Install payment gateways with default configuration';

    public function handle()
    {
        $this->info('Installing payment gateways...');

        $gateways = [
            [
                'name' => 'Stripe',
                'driver' => 'stripe',
                'is_enabled' => false,
                'sort_order' => 1,
                'credentials' => [],
                'settings' => [
                    'payment_methods' => ['card', 'google_pay', 'apple_pay', 'klarna'],
                ],
            ],
            [
                'name' => 'PayPal',
                'driver' => 'paypal',
                'is_enabled' => false,
                'sort_order' => 2,
                'credentials' => [],
                'settings' => [
                    'payment_methods' => ['paypal'],
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['driver' => $gateway['driver']],
                $gateway
            );
            $this->info("✓ Installed {$gateway['name']} gateway");
        }

        $this->info('');
        $this->info('Payment gateways installed successfully!');
        $this->info('Configure them at: /admin/payment-gateways');
    }
}
