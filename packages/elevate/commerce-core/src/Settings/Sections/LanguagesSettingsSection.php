<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class LanguagesSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'languages';
    }

    public function name(): string
    {
        return 'Languages';
    }

    public function description(): string
    {
        return 'Manage store languages and translations';
    }

    public function icon(): string
    {
        return 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'languages');
    }

    public function group(): string
    {
        return 'store';
    }

    public function order(): int
    {
        return 20;
    }
}
