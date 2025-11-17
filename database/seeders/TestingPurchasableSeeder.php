<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TestingPurchasable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TestingPurchasableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fetching products from jsonfakery.com...');
        
        try {
            // Fetch products from the API
            $response = Http::get('https://jsonfakery.com/products');
            
            if (!$response->successful()) {
                $this->command->error('Failed to fetch products from API');
                return;
            }
            
            $apiProducts = $response->json();
            
            if (empty($apiProducts)) {
                $this->command->warn('No products returned from API');
                return;
            }
            
            $this->command->info('Found ' . count($apiProducts) . ' products from API');
            
            // Get default product template if exists
            $defaultTemplate = \ElevateCommerce\Editor\Models\Template::where('model_type', TestingPurchasable::class)
                ->first();
            
            foreach ($apiProducts as $index => $apiProduct) {
                $price = (int) ($apiProduct['price'] * 100); // Convert to cents
                $name = $apiProduct['name'];
                $baseSlug = Str::slug($name);
                
                // Ensure unique slug by appending index if needed
                $slug = $baseSlug;
                $counter = 1;
                while (TestingPurchasable::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                TestingPurchasable::create([
                    'name' => $name,
                    'slug' => $slug,
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'description' => $apiProduct['description'],
                    'price' => $price,
                    'compare_at_price' => (int) ($price * 1.3), // 30% markup
                    'is_active' => true,
                    'stock_quantity' => 100,
                    'track_inventory' => true,
                    'image_url' => $apiProduct['image'] ?? null,
                    'template_id' => $defaultTemplate?->id,
                    'options' => [
                        'manufacturer' => $apiProduct['manufacturer'] ?? null,
                        'category' => $apiProduct['product_category']['name'] ?? null,
                    ],
                ]);
            }
            
            $this->command->info('Successfully created ' . count($apiProducts) . ' testing purchasable products!');
            
        } catch (\Exception $e) {
            $this->command->error('Error seeding products: ' . $e->getMessage());
            
            // Fallback to hardcoded products if API fails
            $this->command->info('Falling back to hardcoded products...');
            $this->seedFallbackProducts();
        }
    }
    
    /**
     * Seed fallback products if API fails
     */
    protected function seedFallbackProducts(): void
    {
        // Get default product template if exists
        $defaultTemplate = \ElevateCommerce\Editor\Models\Template::where('model_type', TestingPurchasable::class)
            ->first();
        
        $products = [
            [
                'name' => 'Premium Cotton T-Shirt',
                'slug' => 'premium-cotton-t-shirt',
                'sku' => 'TSHIRT-001',
                'description' => 'High-quality, comfortable cotton t-shirt. Perfect for everyday wear. Available in multiple colors and sizes.',
                'price' => 2000, // $20.00
                'compare_at_price' => 2999, // $29.99
                'is_active' => true,
                'stock_quantity' => 100,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/3B82F6/FFFFFF?text=T-Shirt',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'colors' => ['Black', 'White', 'Blue', 'Red'],
                ],
            ],
            [
                'name' => 'Classic Denim Jeans',
                'slug' => 'classic-denim-jeans',
                'sku' => 'JEANS-001',
                'description' => 'Comfortable and durable denim jeans with a classic fit. Made from premium quality denim fabric.',
                'price' => 4500, // $45.00
                'compare_at_price' => 5999, // $59.99
                'is_active' => true,
                'stock_quantity' => 75,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/1E40AF/FFFFFF?text=Jeans',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'colors' => ['Dark Blue', 'Light Blue', 'Black'],
                ],
            ],
            [
                'name' => 'Leather Wallet',
                'slug' => 'leather-wallet',
                'sku' => 'WALLET-001',
                'description' => 'Genuine leather wallet with multiple card slots and bill compartments. Elegant and practical.',
                'price' => 3500, // $35.00
                'compare_at_price' => null,
                'is_active' => true,
                'stock_quantity' => 50,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/92400E/FFFFFF?text=Wallet',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'colors' => ['Brown', 'Black', 'Tan'],
                ],
            ],
            [
                'name' => 'Running Shoes',
                'slug' => 'running-shoes',
                'sku' => 'SHOES-001',
                'description' => 'Lightweight and comfortable running shoes with excellent cushioning and support for all-day wear.',
                'price' => 7500, // $75.00
                'compare_at_price' => 9999, // $99.99
                'is_active' => true,
                'stock_quantity' => 60,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/DC2626/FFFFFF?text=Shoes',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'sizes' => ['7', '8', '9', '10', '11', '12'],
                    'colors' => ['Black', 'White', 'Red', 'Blue'],
                ],
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'wireless-headphones',
                'sku' => 'HEADPHONES-001',
                'description' => 'Premium wireless headphones with active noise cancellation and superior sound quality.',
                'price' => 15000, // $150.00
                'compare_at_price' => 19999, // $199.99
                'is_active' => true,
                'stock_quantity' => 30,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/000000/FFFFFF?text=Headphones',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'colors' => ['Black', 'Silver', 'Rose Gold'],
                ],
            ],
            [
                'name' => 'Backpack',
                'slug' => 'backpack',
                'sku' => 'BACKPACK-001',
                'description' => 'Durable and spacious backpack with multiple compartments. Perfect for travel or daily use.',
                'price' => 5500, // $55.00
                'compare_at_price' => null,
                'is_active' => true,
                'stock_quantity' => 40,
                'track_inventory' => true,
                'image_url' => 'https://via.placeholder.com/400x400/059669/FFFFFF?text=Backpack',
                'template_id' => $defaultTemplate?->id,
                'options' => [
                    'colors' => ['Black', 'Gray', 'Navy'],
                ],
            ],
        ];

        foreach ($products as $product) {
            TestingPurchasable::create($product);
        }

        $this->command->info('Created ' . count($products) . ' fallback test purchasable products!');
    }
}
