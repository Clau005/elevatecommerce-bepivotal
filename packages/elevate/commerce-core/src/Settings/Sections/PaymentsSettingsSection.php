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
        return 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z';
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
