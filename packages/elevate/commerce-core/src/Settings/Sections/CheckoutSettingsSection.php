<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class CheckoutSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'checkout';
    }

    public function name(): string
    {
        return 'Checkout';
    }

    public function description(): string
    {
        return 'Customize checkout form fields, marketing options, and checkout rules';
    }

    public function icon(): string
    {
        return 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'checkout');
    }

    public function group(): string
    {
        return 'sales';
    }

    public function order(): int
    {
        return 50;
    }
}
