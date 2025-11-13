<?php

namespace Elevate\Watches\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Watches\Models\Watch;
use Elevate\Editor\Models\Template;

class SeedWatchesCommand extends Command
{
    protected $signature = 'watches:seed';
    protected $description = 'Seed sample watches for testing';

    public function handle()
    {
        $this->info('Seeding sample watches...');

        // Get the default watch template
        $template = Template::where('model_type', Watch::class)
            ->where('is_default', true)
            ->first();

        $watches = [
            [
                'name' => 'Rolex Submariner',
                'slug' => 'rolex-submariner',
                'description' => 'The Rolex Submariner is a legendary diving watch with exceptional water resistance and timeless design. Perfect for both professional divers and watch enthusiasts.',
                'brand' => 'Rolex',
                'model_number' => '126610LN',
                'movement_type' => 'Automatic',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 41,
                'water_resistance' => 300,
                'strap_material' => 'Stainless Steel',
                'price' => 8950.00,
                'compare_at_price' => 9500.00,
                'sku' => 'ROL-SUB-001',
                'stock_quantity' => 5,
                'status' => 'active',
                'is_featured' => true,
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Omega Speedmaster Professional',
                'slug' => 'omega-speedmaster-professional',
                'description' => 'The legendary Moonwatch. First watch worn on the moon, featuring a manual-winding chronograph movement and iconic design.',
                'brand' => 'Omega',
                'model_number' => '310.30.42.50.01.001',
                'movement_type' => 'Manual',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 42,
                'water_resistance' => 50,
                'strap_material' => 'Stainless Steel',
                'price' => 6800.00,
                'sku' => 'OMG-SPD-001',
                'stock_quantity' => 8,
                'status' => 'active',
                'is_featured' => true,
                'template_id' => $template?->id,
            ],
            [
                'name' => 'TAG Heuer Carrera',
                'slug' => 'tag-heuer-carrera',
                'description' => 'A racing-inspired chronograph with elegant design and precision engineering. Perfect for motorsport enthusiasts.',
                'brand' => 'TAG Heuer',
                'model_number' => 'CBN2A1A.BA0643',
                'movement_type' => 'Automatic',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 44,
                'water_resistance' => 100,
                'strap_material' => 'Stainless Steel',
                'price' => 5450.00,
                'sku' => 'TAG-CAR-001',
                'stock_quantity' => 12,
                'status' => 'active',
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Seiko Presage Cocktail Time',
                'slug' => 'seiko-presage-cocktail-time',
                'description' => 'An elegant dress watch with a stunning sunburst dial inspired by cocktails. Excellent value for money with Japanese craftsmanship.',
                'brand' => 'Seiko',
                'model_number' => 'SRPB41',
                'movement_type' => 'Automatic',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 40.5,
                'water_resistance' => 50,
                'strap_material' => 'Leather',
                'price' => 425.00,
                'sku' => 'SEI-PRE-001',
                'stock_quantity' => 20,
                'status' => 'active',
                'is_featured' => true,
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Casio G-Shock GA-2100',
                'slug' => 'casio-g-shock-ga-2100',
                'description' => 'The "CasiOak" - a modern G-Shock with an octagonal case design. Tough, reliable, and stylish for everyday wear.',
                'brand' => 'Casio',
                'model_number' => 'GA-2100-1A1',
                'movement_type' => 'Quartz',
                'case_material' => 'Resin',
                'case_diameter' => 45.4,
                'water_resistance' => 200,
                'strap_material' => 'Rubber',
                'price' => 110.00,
                'sku' => 'CAS-GSH-001',
                'stock_quantity' => 50,
                'status' => 'active',
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Patek Philippe Nautilus',
                'slug' => 'patek-philippe-nautilus',
                'description' => 'The ultimate luxury sports watch. Iconic porthole design with exceptional craftsmanship and prestige.',
                'brand' => 'Patek Philippe',
                'model_number' => '5711/1A-010',
                'movement_type' => 'Automatic',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 40,
                'water_resistance' => 120,
                'strap_material' => 'Stainless Steel',
                'price' => 34890.00,
                'sku' => 'PAT-NAU-001',
                'stock_quantity' => 2,
                'status' => 'active',
                'is_featured' => true,
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Tudor Black Bay Fifty-Eight',
                'slug' => 'tudor-black-bay-58',
                'description' => 'A vintage-inspired dive watch with modern reliability. Compact size and classic design make it perfect for any occasion.',
                'brand' => 'Tudor',
                'model_number' => '79030N',
                'movement_type' => 'Automatic',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 39,
                'water_resistance' => 200,
                'strap_material' => 'Fabric',
                'price' => 3775.00,
                'sku' => 'TUD-BB58-001',
                'stock_quantity' => 6,
                'status' => 'active',
                'template_id' => $template?->id,
            ],
            [
                'name' => 'Citizen Eco-Drive Promaster',
                'slug' => 'citizen-eco-drive-promaster',
                'description' => 'Solar-powered dive watch that never needs a battery. Professional-grade features at an accessible price.',
                'brand' => 'Citizen',
                'model_number' => 'BN0150-28E',
                'movement_type' => 'Quartz',
                'case_material' => 'Stainless Steel',
                'case_diameter' => 44,
                'water_resistance' => 300,
                'strap_material' => 'Rubber',
                'price' => 295.00,
                'sku' => 'CIT-PRO-001',
                'stock_quantity' => 15,
                'status' => 'active',
                'template_id' => $template?->id,
            ],
        ];

        foreach ($watches as $watchData) {
            $watch = Watch::create($watchData);
            $this->info("  ✓ Created: {$watch->name}");
        }

        $this->info('');
        $this->info("✅ Successfully seeded " . count($watches) . " watches!");
        $this->info('');
        $this->info('View them at:');
        $this->info('  - Admin: /admin/watches');
        $this->info('  - Frontend: /watches/{slug}');

        return 0;
    }
}
