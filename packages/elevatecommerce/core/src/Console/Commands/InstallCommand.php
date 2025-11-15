<?php

namespace ElevateCommerce\Core\Console\Commands;

use Illuminate\Console\Command;
use ElevateCommerce\Core\Models\Admin;
use ElevateCommerce\Core\Models\Customer;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevatecommerce-core:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install ElevateCommerce Core - Create admin and customer accounts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 ElevateCommerce Core Installation');
        $this->newLine();

        // Step 1: Configure Authentication
        $this->info('⚙️  Configuring Authentication Guards');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($this->configureAuthGuards()) {
            $this->info('✅ Authentication guards configured');
        } else {
            $this->warn('⚠️  Authentication guards already configured');
        }
        
        $this->newLine();

        // Step 2: Create Admin
        $this->info('📋 Admin Account Setup');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $adminFirstName = $this->ask('Admin First Name', 'Admin');
        $adminLastName = $this->ask('Admin Last Name', 'User');
        $adminEmail = $this->ask('Admin Email', 'admin@example.com');
        $adminPassword = $this->secret('Admin Password (min 8 characters)');
        
        if (strlen($adminPassword) < 8) {
            $this->error('❌ Password must be at least 8 characters');
            return self::FAILURE;
        }

        $adminPasswordConfirm = $this->secret('Confirm Admin Password');
        
        if ($adminPassword !== $adminPasswordConfirm) {
            $this->error('❌ Passwords do not match');
            return self::FAILURE;
        }

        $isSuperAdmin = $this->confirm('Make this admin a Super Admin?', true);

        try {
            $admin = Admin::create([
                'first_name' => $adminFirstName,
                'last_name' => $adminLastName,
                'email' => $adminEmail,
                'password' => bcrypt($adminPassword),
                'is_super_admin' => $isSuperAdmin,
            ]);

            $this->info('✅ Admin created successfully!');
            $this->line("   Email: {$admin->email}");
            $this->line("   Role: " . ($isSuperAdmin ? 'Super Admin' : 'Admin'));
        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();

        // Create Customer
        if ($this->confirm('Create a test customer account?', true)) {
            $this->info('📋 Customer Account Setup');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            
            $customerFirstName = $this->ask('Customer First Name', 'John');
            $customerLastName = $this->ask('Customer Last Name', 'Doe');
            $customerEmail = $this->ask('Customer Email', 'customer@example.com');
            $customerPassword = $this->secret('Customer Password (min 8 characters)');
            
            if (strlen($customerPassword) < 8) {
                $this->error('❌ Password must be at least 8 characters');
                return self::FAILURE;
            }

            $customerPasswordConfirm = $this->secret('Confirm Customer Password');
            
            if ($customerPassword !== $customerPasswordConfirm) {
                $this->error('❌ Passwords do not match');
                return self::FAILURE;
            }

            try {
                $customer = Customer::create([
                    'first_name' => $customerFirstName,
                    'last_name' => $customerLastName,
                    'email' => $customerEmail,
                    'password' => bcrypt($customerPassword),
                ]);

                $this->info('✅ Customer created successfully!');
                $this->line("   Email: {$customer->email}");
            } catch (\Exception $e) {
                $this->error('❌ Failed to create customer: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('🎉 ElevateCommerce Core installation complete!');
        $this->newLine();
        $this->line('Access URLs:');
        $this->line('  Admin Panel: ' . url('/admin/login'));
        $this->line('  Customer Account: ' . url('/account/login'));
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Configure authentication guards in config/auth.php
     */
    protected function configureAuthGuards(): bool
    {
        $authConfigPath = config_path('auth.php');
        
        if (!file_exists($authConfigPath)) {
            $this->error('❌ Auth config file not found');
            return false;
        }

        $authConfig = file_get_contents($authConfigPath);
        
        // Check if admin guard already exists
        if (strpos($authConfig, "'admin' =>") !== false) {
            return false; // Already configured
        }

        // Add admin guard
        $authConfig = preg_replace(
            "/'guards' => \[\s*'web' => \[\s*'driver' => 'session',\s*'provider' => 'users',\s*\],/",
            "'guards' => [\n        'web' => [\n            'driver' => 'session',\n            'provider' => 'users',\n        ],\n\n        'admin' => [\n            'driver' => 'session',\n            'provider' => 'admins',\n        ],",
            $authConfig
        );

        // Add admins provider
        $authConfig = preg_replace(
            "/'providers' => \[\s*'users' => \[\s*'driver' => 'eloquent',\s*'model' => [^,]+,\s*\],/",
            "'providers' => [\n        'users' => [\n            'driver' => 'eloquent',\n            'model' => env('AUTH_MODEL', App\\Models\\User::class),\n        ],\n\n        'admins' => [\n            'driver' => 'eloquent',\n            'model' => ElevateCommerce\\Core\\Models\\Admin::class,\n        ],",
            $authConfig
        );

        // Add admins password reset config
        $authConfig = preg_replace(
            "/('passwords' => \[\s*'users' => \[\s*'provider' => 'users',\s*'table' => [^,]+,\s*'expire' => \d+,\s*'throttle' => \d+,\s*\],)/",
            "$1\n\n        'admins' => [\n            'provider' => 'admins',\n            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),\n            'expire' => 60,\n            'throttle' => 60,\n        ],",
            $authConfig
        );

        file_put_contents($authConfigPath, $authConfig);
        
        return true;
    }
}
