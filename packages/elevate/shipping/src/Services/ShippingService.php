<?php

namespace Elevate\Shipping\Services;

use Elevate\Shipping\Models\ShippingCarrier;
use ShipEngine\ShipEngine;

class ShippingService
{
    /**
     * Get all enabled shipping carriers
     */
    public function getEnabledCarriers()
    {
        return ShippingCarrier::getEnabled();
    }

    /**
     * Create a ShipEngine client instance
     */
    public function createClient(ShippingCarrier $carrier): ShipEngine
    {
        $credentials = $carrier->getActiveCredentials();
        
        $config = [
            'apiKey' => $credentials['api_key'] ?? '',
            'baseUrl' => $carrier->test_mode 
                ? 'https://api.shipengine.com/v1' 
                : 'https://api.shipengine.com/v1',
            'timeout' => 30,
        ];

        return new ShipEngine($config);
    }

    /**
     * Get shipping rates from multiple carriers
     */
    public function getRates(array $shipment)
    {
        $carriers = $this->getEnabledCarriers();
        $rates = [];

        foreach ($carriers as $carrier) {
            try {
                $client = $this->createClient($carrier);
                
                $response = $client->getRatesWithShipmentDetails([
                    'shipment' => $shipment,
                    'rate_options' => [
                        'carrier_ids' => [$carrier->getActiveCredentials()['carrier_id'] ?? null],
                    ],
                ]);

                foreach ($response->rate_response->rates ?? [] as $rate) {
                    $rates[] = [
                        'carrier_id' => $carrier->id,
                        'carrier_name' => $carrier->name,
                        'service_code' => $rate->service_code,
                        'service_type' => $rate->service_type,
                        'amount' => $rate->shipping_amount->amount,
                        'currency' => $rate->shipping_amount->currency,
                        'delivery_days' => $rate->delivery_days ?? null,
                        'estimated_delivery_date' => $rate->estimated_delivery_date ?? null,
                        'rate_id' => $rate->rate_id,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Shipping rate error for {$carrier->name}: " . $e->getMessage());
            }
        }

        // Sort by price
        usort($rates, fn($a, $b) => $a['amount'] <=> $b['amount']);

        return $rates;
    }

    /**
     * Create a shipping label
     */
    public function createLabel(int $carrierId, string $rateId, array $shipment)
    {
        $carrier = ShippingCarrier::findOrFail($carrierId);
        
        if (!$carrier->is_enabled) {
            throw new \Exception('Shipping carrier is disabled');
        }

        $client = $this->createClient($carrier);

        try {
            $response = $client->createLabelFromRate([
                'rate_id' => $rateId,
                'validate_address' => 'validate_and_clean',
            ]);

            return [
                'success' => true,
                'label_id' => $response->label_id,
                'tracking_number' => $response->tracking_number,
                'label_download_url' => $response->label_download->pdf ?? null,
                'shipment_cost' => $response->shipment_cost->amount ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Track a shipment
     */
    public function trackShipment(int $carrierId, string $trackingNumber)
    {
        $carrier = ShippingCarrier::findOrFail($carrierId);
        $client = $this->createClient($carrier);

        try {
            $response = $client->trackUsingLabelId([
                'label_id' => $trackingNumber,
            ]);

            return [
                'success' => true,
                'status' => $response->status_description ?? 'Unknown',
                'events' => $response->events ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate an address
     */
    public function validateAddress(array $address)
    {
        $carrier = ShippingCarrier::where('is_enabled', true)->first();
        
        if (!$carrier) {
            throw new \Exception('No shipping carriers enabled');
        }

        $client = $this->createClient($carrier);

        try {
            $response = $client->validateAddresses([$address]);
            
            return [
                'success' => true,
                'valid' => $response[0]->status === 'verified',
                'address' => $response[0]->matched_address ?? $address,
                'messages' => $response[0]->messages ?? [],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
