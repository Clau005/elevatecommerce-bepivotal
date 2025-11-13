<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class DiscountsSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'discounts';
    }

    public function name(): string
    {
        return 'Discounts';
    }

    public function description(): string
    {
        return 'Create and manage coupons, promotions, and automatic discounts';
    }

    public function icon(): string
    {
        return 'M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'discounts');
    }

    public function group(): string
    {
        return 'commerce';
    }

    public function order(): int
    {
        return 10;
    }
}
