<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Elevate\CommerceCore\Models\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default Online Store channel
        Channel::updateOrCreate(
            ['handle' => 'online-store'],
            [
                'name' => 'Online Store',
                'url' => config('app.url'),
                'default' => true,
            ]
        );

        $this->command->info('✅ Online Store channel created and set as default');
        
        // You can add more channels here in the future
        // Example: POS, Marketplace, Wholesale, etc.
    }
}
