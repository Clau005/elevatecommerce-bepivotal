<?php

namespace ElevateCommerce\Editor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Jobs\SyncThemeSectionsToDatabase;

class InstallCommand extends Command
{
    protected $signature = 'editor:install {--force : Overwrite existing files}';
    
    protected $description = 'Install the Editor package with default theme';

    public function handle()
    {
        $this->info('Installing Editor package...');
        $this->newLine();

        // 1. Publish config
        $this->comment('Publishing configuration...');
        $this->call('vendor:publish', [
            '--tag' => 'editor-config',
            '--force' => $this->option('force'),
        ]);

        // 2. Run migrations
        $this->comment('Running migrations...');
        $this->call('migrate');

        // 3. Create default theme
        $this->comment('Creating default theme...');
        $this->createDefaultTheme();

        $this->newLine();
        $this->info('✓ Editor package installed successfully!');
        $this->newLine();
        
        $this->line('Next steps:');
        $this->line('  1. Visit /admin/themes to manage themes');
        $this->line('  2. Visit /admin/pages to create pages');
        $this->line('  3. Visit /admin/templates to manage templates');

        return 0;
    }

    protected function createDefaultTheme(): void
    {
        // Check if default theme already exists
        $existingTheme = Theme::where('slug', 'default')->first();
        
        if ($existingTheme && !$this->option('force')) {
            $this->warn('Default theme already exists. Use --force to recreate.');
            return;
        }

        if ($existingTheme) {
            $this->warn('Deleting existing default theme...');
            $existingTheme->delete();
        }

        // Create default theme
        $theme = Theme::create([
            'name' => 'Default Theme',
            'slug' => 'default',
            'description' => 'Default theme with basic sections and layouts',
            'version' => '1.0.0',
            'author' => 'ElevateCommerce',
            'is_active' => true,
        ]);

        // Copy theme files from package to project
        $this->copyDefaultThemeFiles($theme->slug);

        // Sync sections to database
        $this->comment('Syncing theme sections...');
        $syncJob = new SyncThemeSectionsToDatabase($theme);
        $syncJob->handle();

        // Create default pages
        $this->comment('Creating default pages...');
        $this->createDefaultPages($theme);

        // Create default templates
        $this->comment('Creating default templates...');
        $this->createDefaultTemplates();

        $this->info("✓ Default theme '{$theme->name}' created and activated");
    }

    protected function copyDefaultThemeFiles(string $slug): void
    {
        $sourcePath = base_path('packages/elevatecommerce/editor/resources/themes/default');
        $destinationPath = resource_path("views/themes/{$slug}");

        // Create theme directory
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Copy sections, snippets, and layouts
        foreach (['sections', 'snippets', 'layouts'] as $folder) {
            $source = "{$sourcePath}/{$folder}";
            $destination = "{$destinationPath}/{$folder}";

            if (File::exists($source)) {
                if (File::exists($destination) && !$this->option('force')) {
                    $this->warn("Skipping {$folder} (already exists)");
                    continue;
                }
                
                File::copyDirectory($source, $destination);
                $this->line("  ✓ Copied {$folder}");
            }
        }
    }

    protected function createDefaultPages($theme): void
    {
        // Homepage with hero and features sections
        \ElevateCommerce\Editor\Models\Page::create([
            'theme_id' => $theme->id,
            'title' => 'Home',
            'slug' => 'home',
            'excerpt' => 'Welcome to your homepage',
            'meta_title' => 'Home - ' . $theme->name,
            'meta_description' => 'Welcome to ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => 'Home',
                    'slug' => 'home',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Home - ' . $theme->name,
                    'meta_description' => 'Welcome to ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ' . $theme->name,
                            'subtitle' => 'Build beautiful websites with our powerful editor',
                            'button_text' => 'Get Started',
                            'button_url' => '#',
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
                            'subtitle' => 'Everything you need to succeed',
                            'features' => [
                                [
                                    'title' => 'Easy to Use',
                                    'description' => 'Intuitive drag-and-drop interface',
                                    'icon' => 'drag'
                                ],
                                [
                                    'title' => 'Responsive',
                                    'description' => 'Looks great on all devices',
                                    'icon' => 'device'
                                ],
                                [
                                    'title' => 'Fast',
                                    'description' => 'Optimized for performance',
                                    'icon' => 'speed'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'Home',
                    'slug' => 'home',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Home - ' . $theme->name,
                    'meta_description' => 'Welcome to ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ' . $theme->name,
                            'subtitle' => 'Build beautiful websites with our powerful editor',
                            'button_text' => 'Get Started',
                            'button_url' => '#',
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
                            'subtitle' => 'Everything you need to succeed',
                            'features' => [
                                [
                                    'title' => 'Easy to Use',
                                    'description' => 'Intuitive drag-and-drop interface',
                                    'icon' => 'drag'
                                ],
                                [
                                    'title' => 'Responsive',
                                    'description' => 'Looks great on all devices',
                                    'icon' => 'device'
                                ],
                                [
                                    'title' => 'Fast',
                                    'description' => 'Optimized for performance',
                                    'icon' => 'speed'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'status' => 'draft',
            'is_active' => false,
        ]);
        $this->line("  ✓ Created page: Home");

        // About page with text section
        \ElevateCommerce\Editor\Models\Page::create([
            'theme_id' => $theme->id,
            'title' => 'About',
            'slug' => 'about',
            'excerpt' => 'Learn more about us',
            'meta_title' => 'About - ' . $theme->name,
            'meta_description' => 'Learn more about ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => 'About',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About - ' . $theme->name,
                    'meta_description' => 'Learn more about ' . $theme->name
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
                    'title' => 'About',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About - ' . $theme->name,
                    'meta_description' => 'Learn more about ' . $theme->name
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
            'status' => 'draft',
            'is_active' => false,
        ]);
        $this->line("  ✓ Created page: About");

        // Contact page with text section
        \ElevateCommerce\Editor\Models\Page::create([
            'theme_id' => $theme->id,
            'title' => 'Contact',
            'slug' => 'contact',
            'excerpt' => 'Get in touch',
            'meta_title' => 'Contact - ' . $theme->name,
            'meta_description' => 'Contact ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact - ' . $theme->name,
                    'meta_description' => 'Contact ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . (time() + 2),
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
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact - ' . $theme->name,
                    'meta_description' => 'Contact ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . (time() + 2),
                        'component' => 'text',
                        'data' => [
                            'title' => 'Get In Touch',
                            'content' => '<p>We\'d love to hear from you. Contact us today!</p>'
                        ]
                    ]
                ]
            ],
            'status' => 'draft',
            'is_active' => false,
        ]);
        $this->line("  ✓ Created page: Contact");
    }

    protected function createDefaultTemplates(): void
    {
        $templates = [
            [
                'name' => 'Product Template',
                'slug' => 'product',
                'model_type' => 'App\\Models\\Product',
                'description' => 'Default template for product pages',
            ],
            [
                'name' => 'Collection Template',
                'slug' => 'collection',
                'model_type' => 'App\\Models\\Collection',
                'description' => 'Default template for collection pages',
            ],
        ];

        foreach ($templates as $templateData) {
            \ElevateCommerce\Editor\Models\Template::firstOrCreate(
                ['slug' => $templateData['slug']],
                [
                    'name' => $templateData['name'],
                    'model_type' => $templateData['model_type'],
                    'description' => $templateData['description'],
                    'configuration' => [
                        'sections' => []
                    ],
                    'draft_configuration' => [
                        'sections' => []
                    ],
                    'status' => 'draft',
                    'is_default' => true,
                ]
            );
            
            $this->line("  ✓ Created template: {$templateData['name']}");
        }


           // Collection Template
        \ElevateCommerce\Editor\Models\Template::firstOrCreate(
            ['slug' => 'collection'],
            [
                'name' => 'Collection Template',
                'model_type' => 'ElevateCommerce\\Collections\\Models\\Collection',
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
                'status' => 'draft',
                'is_default' => true,
            ]
        );

        // Product Template
        \ElevateCommerce\Editor\Models\Template::firstOrCreate(
            ['slug' => 'product'],
            [
                'name' => 'Product Template',
                'model_type' => 'ElevateCommerce\\Product\\Models\\Product',
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
                'draft_configuration' =>  [
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
                'status' => 'draft',
                'is_default' => true,
            ]
        );
    }
}
