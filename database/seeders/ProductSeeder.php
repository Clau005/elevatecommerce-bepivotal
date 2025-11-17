<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Elevate\Product\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Classic Leather Watch',
                'slug' => 'classic-leather-watch',
                'sku' => 'WATCH-001',
                'description' => 'A timeless classic watch featuring a genuine leather strap and precision quartz movement. Perfect for both casual and formal occasions.',
                'short_description' => 'Timeless classic with leather strap',
                'type' => 'simple',
                'status' => 'active',
                'price' => 15000, // $150.00 in cents
                'compare_at_price' => 20000, // $200.00
                'cost_per_item' => 7500, // $75.00
                'track_inventory' => true,
                'stock' => 50,
                'weight' => 200, // 200g
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20, // 20% VAT
                'featured_image' => '/images/products/classic-leather-watch.jpg',
            ],
            [
                'name' => 'Sport Chronograph Watch',
                'slug' => 'sport-chronograph-watch',
                'sku' => 'WATCH-002',
                'description' => 'Durable sport watch with chronograph functionality and 100m water resistance. Built for active lifestyles with scratch-resistant sapphire crystal.',
                'short_description' => 'Waterproof sport chronograph',
                'type' => 'simple',
                'status' => 'active',
                'price' => 25000, // $250.00
                'compare_at_price' => 30000, // $300.00
                'cost_per_item' => 12500,
                'track_inventory' => true,
                'stock' => 30,
                'weight' => 180,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/sport-chronograph-watch.jpg',
            ],
            [
                'name' => 'Luxury Automatic Watch',
                'slug' => 'luxury-automatic-watch',
                'sku' => 'WATCH-003',
                'description' => 'Premium luxury watch with automatic movement and exhibition case back. Features Swiss-made movement and 316L stainless steel case.',
                'short_description' => 'Swiss automatic movement',
                'type' => 'simple',
                'status' => 'active',
                'price' => 50000, // $500.00
                'compare_at_price' => 65000, // $650.00
                'cost_per_item' => 25000,
                'track_inventory' => true,
                'stock' => 10,
                'weight' => 250,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/luxury-automatic-watch.jpg',
            ],
            [
                'name' => 'Minimalist Dress Watch',
                'slug' => 'minimalist-dress-watch',
                'sku' => 'WATCH-004',
                'description' => 'Sleek minimalist design with ultra-thin case and mesh bracelet. Perfect for formal occasions and business wear.',
                'short_description' => 'Ultra-thin minimalist design',
                'type' => 'simple',
                'status' => 'active',
                'price' => 18000, // $180.00
                'cost_per_item' => 9000,
                'track_inventory' => true,
                'stock' => 40,
                'weight' => 150,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/minimalist-dress-watch.jpg',
            ],
            [
                'name' => 'Dive Watch Pro',
                'slug' => 'dive-watch-pro',
                'sku' => 'WATCH-005',
                'description' => 'Professional dive watch with 300m water resistance, unidirectional bezel, and luminous markers. ISO 6425 certified.',
                'short_description' => 'Professional dive watch 300m',
                'type' => 'simple',
                'status' => 'active',
                'price' => 35000, // $350.00
                'compare_at_price' => 42000,
                'cost_per_item' => 17500,
                'track_inventory' => true,
                'stock' => 20,
                'weight' => 220,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/dive-watch-pro.jpg',
            ],
            [
                'name' => 'Smart Hybrid Watch',
                'slug' => 'smart-hybrid-watch',
                'sku' => 'WATCH-006',
                'description' => 'Hybrid smartwatch combining classic analog design with smart features. Activity tracking, notifications, and 6-month battery life.',
                'short_description' => 'Analog smartwatch hybrid',
                'type' => 'simple',
                'status' => 'active',
                'price' => 28000, // $280.00
                'cost_per_item' => 14000,
                'track_inventory' => true,
                'stock' => 35,
                'weight' => 190,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/smart-hybrid-watch.jpg',
            ],
            [
                'name' => 'Vintage Pilot Watch',
                'slug' => 'vintage-pilot-watch',
                'sku' => 'WATCH-007',
                'description' => 'Aviation-inspired vintage pilot watch with large luminous numerals and leather NATO strap. Classic military styling.',
                'short_description' => 'Aviation vintage style',
                'type' => 'simple',
                'status' => 'active',
                'price' => 22000, // $220.00
                'compare_at_price' => 27000,
                'cost_per_item' => 11000,
                'track_inventory' => true,
                'stock' => 25,
                'weight' => 210,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/vintage-pilot-watch.jpg',
            ],
            [
                'name' => 'Rose Gold Fashion Watch',
                'slug' => 'rose-gold-fashion-watch',
                'sku' => 'WATCH-008',
                'description' => 'Elegant rose gold plated watch with crystal-embellished bezel. Sophisticated design for special occasions.',
                'short_description' => 'Rose gold with crystals',
                'type' => 'simple',
                'status' => 'active',
                'price' => 16500, // $165.00
                'cost_per_item' => 8250,
                'track_inventory' => true,
                'stock' => 45,
                'weight' => 160,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/rose-gold-fashion-watch.jpg',
            ],
            [
                'name' => 'Limited Edition Tourbillon',
                'slug' => 'limited-edition-tourbillon',
                'sku' => 'WATCH-009',
                'description' => 'Exclusive limited edition watch featuring visible tourbillon mechanism. Only 100 pieces worldwide. Comes with certificate of authenticity.',
                'short_description' => 'Limited edition tourbillon',
                'type' => 'simple',
                'status' => 'active',
                'price' => 150000, // $1,500.00
                'cost_per_item' => 75000,
                'track_inventory' => true,
                'stock' => 3,
                'weight' => 280,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/limited-edition-tourbillon.jpg',
            ],
            [
                'name' => 'Everyday Casual Watch',
                'slug' => 'everyday-casual-watch',
                'sku' => 'WATCH-010',
                'description' => 'Affordable and reliable everyday watch with canvas strap. Perfect for casual wear and daily activities.',
                'short_description' => 'Casual everyday watch',
                'type' => 'simple',
                'status' => 'active',
                'price' => 8500, // $85.00
                'cost_per_item' => 4250,
                'track_inventory' => true,
                'stock' => 100,
                'weight' => 140,
                'weight_unit' => 'g',
                'requires_shipping' => true,
                'is_taxable' => true,
                'tax_rate' => 0.20,
                'featured_image' => '/images/products/everyday-casual-watch.jpg',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ Created ' . count($products) . ' test products');
        $this->command->info('💰 Price range: $85 - $1,500');
        $this->command->info('📦 Total stock: ' . collect($products)->sum('stock') . ' units');
    }
}
