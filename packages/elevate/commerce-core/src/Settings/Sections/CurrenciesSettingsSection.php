<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class CurrenciesSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'currencies';
    }

    public function name(): string
    {
        return 'Currencies';
    }

    public function description(): string
    {
        return 'Manage currencies, exchange rates, and currency display settings';
    }

    public function icon(): string
    {
        return 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'currencies');
    }

    public function group(): string
    {
        return 'store';
    }

    public function order(): int
    {
        return 15;
    }
}
