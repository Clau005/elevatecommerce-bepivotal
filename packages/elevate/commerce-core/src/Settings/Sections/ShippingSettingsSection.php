<?php

namespace Elevate\CommerceCore\Settings\Sections;

use Elevate\CommerceCore\Settings\SettingsSection;

class ShippingSettingsSection extends SettingsSection
{
    public function id(): string
    {
        return 'shipping';
    }

    public function name(): string
    {
        return 'Shipping';
    }

    public function description(): string
    {
        return 'Configure shipping carriers like UPS, FedEx, USPS, and DHL via ShipEngine';
    }

    public function icon(): string
    {
        return 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0';
    }

    public function url(): string
    {
        return route('admin.shipping-carriers.index');
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
