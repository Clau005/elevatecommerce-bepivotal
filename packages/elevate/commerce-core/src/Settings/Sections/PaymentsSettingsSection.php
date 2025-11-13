<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class PaymentsSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'payments';
    }

    public function name(): string
    {
        return 'Payments';
    }

    public function description(): string
    {
        return 'Configure payment gateways like Stripe, PayPal, Google Pay, and Apple Pay';
    }

    public function icon(): string
    {
        return 'credit-card';
    }

    public function url(): string
    {
        return route('admin.payment-gateways.index');
    }

    public function group(): string
    {
        return 'sales';
    }

    public function order(): int
    {
        return 40;
    }
}
