<?php

namespace ElevateCommerce\Purchasable\Database\Seeders;

use ElevateCommerce\Purchasable\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
