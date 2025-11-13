<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class StatesSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'states';
    }

    public function name(): string
    {
        return 'States & Regions';
    }

    public function description(): string
    {
        return 'Manage countries, states, and regions for shipping and tax';
    }

    public function icon(): string
    {
        return 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'states');
    }

    public function group(): string
    {
        return 'store';
    }

    public function order(): int
    {
        return 25;
    }
}
