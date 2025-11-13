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
        return 'shopping-cart';
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
