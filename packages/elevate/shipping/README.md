# Elevate Shipping

Multi-carrier shipping integration powered by ShipEngine.

## Features

- ✅ **Multiple Carriers**: UPS, FedEx, USPS, DHL Express
- ✅ **Real-time Rates**: Get shipping quotes from all carriers
- ✅ **Label Generation**: Create shipping labels
- ✅ **Address Validation**: Verify shipping addresses
- ✅ **Package Tracking**: Track shipments
- ✅ **Test Mode**: Separate test and live credentials
- ✅ **Admin Interface**: Easy carrier management

## Supported Carriers

- **UPS** - Ground, Next Day Air, 2nd Day Air, Worldwide Express
- **FedEx** - Ground, Express Saver, Priority Overnight, International Economy
- **USPS** - First Class, Priority, Priority Express, Media Mail
- **DHL Express** - Express Worldwide, Express 12:00, Express 9:00

## Installation

1. Install the ShipEngine PHP package:

```bash
composer require shipengine/shipengine-php
```

2. Run migrations:

```bash
php artisan migrate
```

3. Install default carriers:

```bash
php artisan shipping:install
```

4. Sign up for ShipEngine:
   - Visit https://www.shipengine.com/
   - Create an account
   - Get your API key from the dashboard
   - Connect your carrier accounts (UPS, FedEx, etc.)

5. Configure carriers:
   - Visit `/admin/shipping-carriers`
   - Enable carriers
   - Add your ShipEngine API key
   - Add carrier IDs from ShipEngine dashboard

## Configuration

### Admin Interface

Visit `/admin/shipping-carriers` to:
- Enable/disable carriers
- Toggle test mode
- Configure API keys
- Set carrier IDs

### ShipEngine Setup

1. **Get API Key**: Dashboard → API Keys → Create New Key
2. **Connect Carriers**: Dashboard → Carriers → Connect Account
3. **Get Carrier IDs**: Dashboard → Carriers → View Details

## Usage

### Get Shipping Rates

```php
use Elevate\Shipping\Services\ShippingService;

$shippingService = app(ShippingService::class);

$rates = $shippingService->getRates([
    'ship_to' => [
        'name' => 'John Doe',
        'address_line1' => '123 Main St',
        'city_locality' => 'Austin',
        'state_province' => 'TX',
        'postal_code' => '78701',
        'country_code' => 'US',
    ],
    'ship_from' => [
        'name' => 'Your Store',
        'address_line1' => '456 Store St',
        'city_locality' => 'Los Angeles',
        'state_province' => 'CA',
        'postal_code' => '90001',
        'country_code' => 'US',
    ],
    'packages' => [[
        'weight' => [
            'value' => 2.0,
            'unit' => 'pound',
        ],
        'dimensions' => [
            'length' => 10,
            'width' => 8,
            'height' => 6,
            'unit' => 'inch',
        ],
    ]],
]);

// Returns array of rates sorted by price
foreach ($rates as $rate) {
    echo "{$rate['carrier_name']} - {$rate['service_type']}: \${$rate['amount']}\n";
}
```

### Create Shipping Label

```php
$label = $shippingService->createLabel(
    carrierId: 1,
    rateId: 'rate_123456',
    shipment: $shipmentData
);

if ($label['success']) {
    $trackingNumber = $label['tracking_number'];
    $labelUrl = $label['label_download_url'];
}
```

### Track Shipment

```php
$tracking = $shippingService->trackShipment(
    carrierId: 1,
    trackingNumber: '1Z999AA10123456784'
);

echo $tracking['status']; // "In Transit"
```

### Validate Address

```php
$validation = $shippingService->validateAddress([
    'address_line1' => '123 Main St',
    'city_locality' => 'Austin',
    'state_province' => 'TX',
    'postal_code' => '78701',
    'country_code' => 'US',
]);

if ($validation['valid']) {
    $cleanedAddress = $validation['address'];
}
```

## Test Mode

Each carrier supports separate test and live credentials:

- **Test Mode ON**: Uses test API keys, no real charges
- **Test Mode OFF**: Uses live API keys, real shipments

## Security

- All API keys are encrypted in the database
- Separate test and production credentials
- HTTPS required for production

## License

MIT
