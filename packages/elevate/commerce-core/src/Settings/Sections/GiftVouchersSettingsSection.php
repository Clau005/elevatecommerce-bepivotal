<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class GiftVouchersSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'gift-vouchers';
    }

    public function name(): string
    {
        return 'Gift Vouchers';
    }

    public function description(): string
    {
        return 'Create and manage gift vouchers with usage tracking';
    }

    public function icon(): string
    {
        return 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'gift-vouchers');
    }

    public function group(): string
    {
        return 'commerce';
    }

    public function order(): int
    {
        return 30;
    }
}
