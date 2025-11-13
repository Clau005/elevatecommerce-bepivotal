<?php

namespace Elevate\Shipping\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Shipping\Models\ShippingCarrier;

class InstallShippingCommand extends Command
{
    protected $signature = 'shipping:install';
    protected $description = 'Install shipping carriers with default configuration';

    public function handle()
    {
        $this->info('Installing shipping carriers...');

        $carriers = [
            [
                'name' => 'UPS',
                'carrier_code' => 'ups',
                'is_enabled' => false,
                'test_mode' => true,
                'sort_order' => 1,
                'credentials' => [],
                'test_credentials' => [],
                'settings' => [
                    'services' => ['ground', 'next_day_air', '2nd_day_air', 'worldwide_express'],
                ],
            ],
            [
                'name' => 'FedEx',
                'carrier_code' => 'fedex',
                'is_enabled' => false,
                'test_mode' => true,
                'sort_order' => 2,
                'credentials' => [],
                'test_credentials' => [],
                'settings' => [
                    'services' => ['ground', 'express_saver', 'priority_overnight', 'international_economy'],
                ],
            ],
            [
                'name' => 'USPS',
                'carrier_code' => 'usps',
                'is_enabled' => false,
                'test_mode' => true,
                'sort_order' => 3,
                'credentials' => [],
                'test_credentials' => [],
                'settings' => [
                    'services' => ['first_class', 'priority', 'priority_express', 'media_mail'],
                ],
            ],
            [
                'name' => 'DHL Express',
                'carrier_code' => 'dhl_express',
                'is_enabled' => false,
                'test_mode' => true,
                'sort_order' => 4,
                'credentials' => [],
                'test_credentials' => [],
                'settings' => [
                    'services' => ['express_worldwide', 'express_12:00', 'express_9:00'],
                ],
            ],
        ];

        foreach ($carriers as $carrier) {
            ShippingCarrier::updateOrCreate(
                ['carrier_code' => $carrier['carrier_code']],
                $carrier
            );
            $this->info("✓ Installed {$carrier['name']} carrier");
        }

        $this->info('');
        $this->info('Shipping carriers installed successfully!');
        $this->info('Configure them at: /admin/shipping-carriers');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Sign up for ShipEngine at https://www.shipengine.com/');
        $this->info('2. Get your API key from the dashboard');
        $this->info('3. Connect your carrier accounts in ShipEngine');
        $this->info('4. Add your ShipEngine API key in the admin panel');
    }
}
