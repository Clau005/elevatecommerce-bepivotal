<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use ElevateCommerce\Core\Models\Admin;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test customer
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create test admin
        Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        $this->command->info('✅ Created test users:');
        $this->command->info('   Customer: customer@example.com / password');
        $this->command->info('   Admin: admin@example.com / password');
    }
}
