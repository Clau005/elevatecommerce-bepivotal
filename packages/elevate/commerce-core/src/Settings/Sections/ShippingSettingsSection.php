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
        return 'truck';
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
