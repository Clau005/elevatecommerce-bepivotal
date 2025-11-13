<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class StaffSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'staff';
    }

    public function name(): string
    {
        return 'Staff & Permissions';
    }

    public function description(): string
    {
        return 'Manage staff accounts, roles, and permissions';
    }

    public function icon(): string
    {
        return 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z';
    }

    public function url(): string
    {
        return route('admin.settings.show', 'staff');
    }

    public function group(): string
    {
        return 'users';
    }

    public function order(): int
    {
        return 10;
    }
}
