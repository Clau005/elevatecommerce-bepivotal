<?php

namespace Elevate\CommerceCore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Elevate\CommerceCore\Models\Staff;
use Elevate\Editor\Models\Theme;
use Elevate\Editor\Models\Page;
use Elevate\Editor\Models\Template;
use Elevate\Editor\Jobs\SyncThemeSectionsToDatabase;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'elevatecommerce:install 
                            {--email= : Admin email address}
                            {--password= : Admin password}
                            {--name= : Admin name}';

    /**
     * The console command description.
     */
    protected $description = 'Install ElevateCommerce: Create admin staff user and default theme';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Installing ElevateCommerce...');
        $this->newLine();

        // Step 1: Create staff user
        $this->createStaffUser();

        // Step 2: Create default theme
        $this->createDefaultTheme();

        $this->newLine();
        $this->info('✅ ElevateCommerce installed successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Create admin staff user
     */
    protected function createStaffUser(): void
    {
        $this->info('👤 Creating admin staff user...');

        $email = $this->option('email') ?? $this->ask('Admin email', 'admin@example.com');
        $name = $this->option('name') ?? $this->ask('Admin name', 'Admin User');
        $password = $this->option('password') ?? $this->secret('Admin password');

        if (!$password) {
            $password = 'password';
            $this->warn('No password provided, using default: password');
        }

        // Split name into first and last name
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $staff = Staff::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => Hash::make($password),
                'is_super_admin' => true,
            ]
        );

        if ($staff->wasRecentlyCreated) {
            $this->info("✓ Admin staff created: {$email}");
        } else {
            $this->warn("⚠ Staff user already exists: {$email}");
        }
    }

    /**
     * Create default theme with templates and pages
     */
    protected function createDefaultTheme(): void
    {
        $this->info('🎨 Creating default theme...');

        // Check if theme already exists
        $existingTheme = Theme::where('slug', 'default')->first();
        if ($existingTheme) {
            $this->warn('⚠ Default theme already exists, skipping...');
            return;
        }

        // Create theme
        $theme = Theme::create([
            'name' => 'Default',
            'slug' => 'default',
            'description' => 'Default theme for ElevateCommerce',
            'version' => '1.0.0',
            'author' => 'ElevateCommerce',
            'is_active' => true,
        ]);

        $this->info('✓ Theme created: Default');

        // Copy theme files from resources/general to resources/views/themes/default
        $this->copyThemeFiles($theme->slug);
        $this->info('✓ Theme files copied');

        // Sync sections from theme files to database
        $syncJob = new SyncThemeSectionsToDatabase($theme);
        $syncJob->handle();
        $this->info('✓ Sections synced to database');

        // Create default pages
        $this->createDefaultPages($theme);
        $this->info('✓ Default pages created');

        // Create default templates
        $this->createDefaultTemplates();
        $this->info('✓ Default templates created');

        // Activate the theme
        $theme->activate();
        $this->info('✓ Theme activated');
    }

    /**
     * Copy theme files from resources/general to resources/views/themes/{slug}
     */
    protected function copyThemeFiles(string $slug): void
    {
        $sourcePath = resource_path('general');
        $destinationPath = resource_path("views/themes/{$slug}");

        // Create theme directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Copy sections, snippets, and layouts
        foreach (['sections', 'snippets', 'layouts'] as $folder) {
            $source = "{$sourcePath}/{$folder}";
            $destination = "{$destinationPath}/{$folder}";

            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
            }
        }
    }

    /**
     * Create default pages for the theme
     */
    protected function createDefaultPages(Theme $theme): void
    {
        // Homepage with sections
        Page::create([
            'theme_id' => $theme->id,
            'title' => 'Homepage',
            'slug' => 'homepage',
            'excerpt' => 'Welcome to your homepage',
            'meta_title' => 'Home - ElevateCommerce',
            'meta_description' => 'Welcome to ElevateCommerce',
            'configuration' => [
                'basic_info' => [
                    'title' => 'Homepage',
                    'slug' => 'homepage',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Home - ElevateCommerce',
                    'meta_description' => 'Build beautiful e-commerce websites with ElevateCommerce'
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ElevateCommerce',
                            'subtitle' => 'Build stunning e-commerce experiences',
                            'button_text' => 'Shop Now',
                            'button_url' => '/products',
                            'background_color' => '#1f2937',
                            'text_color' => '#ffffff',
                            'height' => 500
                        ]
                    ],
                    [
                        'id' => 'features-' . (time() + 1),
                        'component' => 'features',
                        'data' => [
                            'title' => 'Powerful Features',
                            'subtitle' => 'Everything you need for your online store',
                            'features' => [
                                [
                                    'title' => 'Easy Management',
                                    'description' => 'Manage products, orders, and customers with ease',
                                    'icon' => 'settings'
                                ],
                                [
                                    'title' => 'Flexible Themes',
                                    'description' => 'Customize your store with our visual editor',
                                    'icon' => 'palette'
                                ],
                                [
                                    'title' => 'Secure Payments',
                                    'description' => 'Accept payments with confidence',
                                    'icon' => 'lock'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'Homepage',
                    'slug' => 'homepage',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Home - ElevateCommerce',
                    'meta_description' => 'Build beautiful e-commerce websites with ElevateCommerce'
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ElevateCommerce',
                            'subtitle' => 'Build stunning e-commerce experiences',
                            'button_text' => 'Shop Now',
                            'button_url' => '/products',
                            'background_color' => '#1f2937',
                            'text_color' => '#ffffff',
                            'height' => 500
                        ]
                    ],
                    [
                        'id' => 'features-' . (time() + 1),
                        'component' => 'features',
                        'data' => [
                            'title' => 'Powerful Features',
                            'subtitle' => 'Everything you need for your online store',
                            'features' => [
                                [
                                    'title' => 'Easy Management',
                                    'description' => 'Manage products, orders, and customers with ease',
                                    'icon' => 'settings'
                                ],
                                [
                                    'title' => 'Flexible Themes',
                                    'description' => 'Customize your store with our visual editor',
                                    'icon' => 'palette'
                                ],
                                [
                                    'title' => 'Secure Payments',
                                    'description' => 'Accept payments with confidence',
                                    'icon' => 'lock'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'status' => 'published',
            'is_active' => true,
        ]);

        // About page
        Page::create([
            'theme_id' => $theme->id,
            'title' => 'About Us',
            'slug' => 'about',
            'excerpt' => 'Learn more about us',
            'meta_title' => 'About Us',
            'meta_description' => 'Learn more about our company',
            'configuration' => [
                'basic_info' => [
                    'title' => 'About Us',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About Us',
                    'meta_description' => 'Learn more about our company'
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'About Us',
                            'content' => '<p>Welcome to our about page. Tell your story here.</p>'
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'About Us',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About Us',
                    'meta_description' => 'Learn more about our company'
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'About Us',
                            'content' => '<p>Welcome to our about page. Tell your story here.</p>'
                        ]
                    ]
                ]
            ],
            'status' => 'published',
            'is_active' => true,
        ]);

        // Contact page
        Page::create([
            'theme_id' => $theme->id,
            'title' => 'Contact Us',
            'slug' => 'contact',
            'excerpt' => 'Get in touch with us',
            'meta_title' => 'Contact Us',
            'meta_description' => 'Contact us today',
            'configuration' => [
                'basic_info' => [
                    'title' => 'Contact Us',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact Us',
                    'meta_description' => 'Contact us today'
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'Get In Touch',
                            'content' => '<p>We\'d love to hear from you. Contact us today!</p>'
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'Contact Us',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact Us',
                    'meta_description' => 'Contact us today'
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'Get In Touch',
                            'content' => '<p>We\'d love to hear from you. Contact us today!</p>'
                        ]
                    ]
                ]
            ],
            'status' => 'published',
            'is_active' => true,
        ]);
    }

    /**
     * Create default templates if they don't exist
     */
    protected function createDefaultTemplates(): void
    {
        // Collection Template
        Template::firstOrCreate(
            ['slug' => 'collection'],
            [
                'name' => 'Collection Template',
                'model_type' => 'Elevate\\Collections\\Models\\Collection',
                'description' => 'Default template for collection pages',
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'collection-hero-' . time(),
                            'component' => 'collection-hero',
                            'data' => [
                                'title' => '{{ model.name }}',
                                'subtitle' => '{{ model.description }}',
                                'show_product_count' => true,
                                'background_color' => '#1f2937',
                                'text_color' => '#ffffff',
                                'height' => 400
                            ]
                        ],
                        [
                            'id' => 'collection-grid-' . (time() + 1),
                            'component' => 'collection-grid',
                            'data' => [
                                'columns' => 4,
                                'gap' => '2rem',
                                'show_filters' => true,
                                'show_sort' => true
                            ]
                        ]
                    ]
                ],
                'draft_configuration' => [
                    'basic_info' => [
                        'title' => 'Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'collection-hero-' . time(),
                            'component' => 'collection-hero',
                            'data' => [
                                'title' => '{{ model.name }}',
                                'subtitle' => '{{ model.description }}',
                                'show_product_count' => true,
                                'background_color' => '#1f2937',
                                'text_color' => '#ffffff',
                                'height' => 400
                            ]
                        ],
                        [
                            'id' => 'collection-grid-' . (time() + 1),
                            'component' => 'collection-grid',
                            'data' => [
                                'columns' => 4,
                                'gap' => '2rem',
                                'show_filters' => true,
                                'show_sort' => true
                            ]
                        ]
                    ]
                ],
                'status' => 'published',
                'is_default' => true,
            ]
        );

        // Product Template
        Template::firstOrCreate(
            ['slug' => 'product'],
            [
                'name' => 'Product Template',
                'model_type' => 'Elevate\\Product\\Models\\Product',
                'description' => 'Default template for product pages',
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Product Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'product-show-' . time(),
                            'component' => 'product-show',
                            'data' => [
                                'layout' => 'default',
                                'show_breadcrumbs' => true,
                                'show_related_products' => true,
                                'gallery_style' => 'thumbnails'
                            ]
                        ]
                    ]
                ],
                'draft_configuration' => [
                    'basic_info' => [
                        'title' => 'Product Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'product-show-' . time(),
                            'component' => 'product-show',
                            'data' => [
                                'layout' => 'default',
                                'show_breadcrumbs' => true,
                                'show_related_products' => true,
                                'gallery_style' => 'thumbnails'
                            ]
                        ]
                    ]
                ],
                'status' => 'published',
                'is_default' => true,
            ]
        );
    }
}
