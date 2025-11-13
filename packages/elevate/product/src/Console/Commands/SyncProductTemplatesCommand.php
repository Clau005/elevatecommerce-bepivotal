<?php

namespace Elevate\Product\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Themes\Models\Theme;
use Elevate\Themes\Models\Template;
use Elevate\Themes\Services\ThemeService;
use Illuminate\Support\Facades\File;

class SyncProductTemplatesCommand extends Command
{
    protected $signature = 'products:sync-templates 
                            {direction=files-to-db : Sync direction: files-to-db or db-to-files}
                            {--force : Force overwrite existing templates}';

    protected $description = 'Sync product templates between files and database';

    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        parent::__construct();
        $this->themeService = $themeService;
    }

    public function handle(): int
    {
        $direction = $this->argument('direction');
        $force = $this->option('force');

        // Validate direction
        if (!in_array($direction, ['files-to-db', 'db-to-files'])) {
            $this->error('Invalid direction. Use "files-to-db" or "db-to-files"');
            return 1;
        }

        $this->info("Syncing product templates: {$direction}...");

        // Get all themes
        $themes = Theme::all();

        if ($themes->isEmpty()) {
            $this->warn('No themes found in database.');
            return 1;
        }

        foreach ($themes as $theme) {
            $this->info("Processing theme: {$theme->name} ({$theme->slug})");

            try {
                if ($direction === 'files-to-db') {
                    $this->syncFilesToDatabase($theme, $force);
                } else {
                    $this->syncDatabaseToFiles($theme, $force);
                }
            } catch (\Exception $e) {
                $this->error("Error processing theme {$theme->slug}: {$e->getMessage()}");
                continue;
            }
        }

        $this->info('✓ Product templates synced successfully!');
        return 0;
    }

    /**
     * Sync template files to database
     */
    protected function syncFilesToDatabase(Theme $theme, bool $force): void
    {
        $templatesPath = storage_path("themes/{$theme->slug}/templates");

        // Create templates directory if it doesn't exist
        if (!File::exists($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
            $this->info("  Created templates directory for {$theme->slug}");
            
            // Create default templates
            $this->createDefaultTemplates($theme);
            return;
        }

        // Get all product template files
        $templateFiles = File::glob("{$templatesPath}/product*.json");

        if (empty($templateFiles)) {
            $this->warn("  No product template files found in {$theme->slug}");
            $this->createDefaultTemplates($theme);
            return;
        }

        foreach ($templateFiles as $filePath) {
            $fileName = basename($filePath, '.json');
            
            // Read template file
            $content = File::get($filePath);
            $templateData = json_decode($content, true);

            if (!$templateData) {
                $this->warn("  Skipping invalid JSON file: {$fileName}");
                continue;
            }

            // Prepare template data for database
            $dbData = [
                'slug' => $fileName,
                'theme_id' => $theme->id,
            ];

            // Add metadata from JSON
            $dbData['name'] = $templateData['name'] ?? ucfirst(str_replace('-', ' ', $fileName));
            $dbData['description'] = $templateData['description'] ?? null;
            $dbData['type'] = 'product';
            $dbData['route_pattern'] = $templateData['route_pattern'] ?? '/products/{slug}';
            $dbData['model_binding'] = 'Elevate\Product\Models\Product';
            $dbData['is_active'] = $templateData['is_active'] ?? true;
            $dbData['configuration'] = $templateData;

            // Create or update template
            $template = Template::updateOrCreate(
                [
                    'slug' => $fileName,
                    'theme_id' => $theme->id,
                ],
                $dbData
            );

            $this->info("  ✓ Synced template: {$template->name}");
        }
    }

    /**
     * Sync database templates to files
     */
    protected function syncDatabaseToFiles(Theme $theme, bool $force): void
    {
        $templatesPath = storage_path("themes/{$theme->slug}/templates");

        // Create templates directory if it doesn't exist
        if (!File::exists($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
        }

        // Get all product templates from database
        $templates = Template::where('theme_id', $theme->id)
            ->where('type', 'product')
            ->get();

        if ($templates->isEmpty()) {
            $this->warn("  No product templates found in database for {$theme->slug}");
            return;
        }

        foreach ($templates as $template) {
            $filePath = "{$templatesPath}/{$template->slug}.json";

            // Check if file exists and force is not set
            if (File::exists($filePath) && !$force) {
                $this->warn("  Skipping existing file: {$template->slug}.json (use --force to overwrite)");
                continue;
            }

            // Write template to file
            $content = json_encode($template->configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            File::put($filePath, $content);

            $this->info("  ✓ Wrote template file: {$template->slug}.json");
        }
    }

    /**
     * Create default product templates
     */
    protected function createDefaultTemplates(Theme $theme): void
    {
        $this->info("  Creating default product templates for {$theme->slug}...");

        $templates = [
            'product' => [
                'name' => 'Product - Default',
                'slug' => 'product',
                'description' => 'Default product page template with full details',
                'type' => 'product',
                'route_pattern' => '/products/{slug}',
                'model_binding' => 'Elevate\Product\Models\Product',
                'is_active' => true,
                'sections' => [
                    [
                        'id' => 'product-hero',
                        'type' => 'product-hero',
                        'settings' => [
                            'show_breadcrumbs' => true,
                            'image_position' => 'left',
                            'image_size' => 'large',
                            'enable_zoom' => true,
                            'show_gallery' => true,
                        ],
                    ],
                    [
                        'id' => 'product-details',
                        'type' => 'product-details',
                        'settings' => [
                            'show_sku' => true,
                            'show_vendor' => true,
                            'show_availability' => true,
                            'show_share_buttons' => true,
                        ],
                    ],
                    [
                        'id' => 'product-description',
                        'type' => 'product-description',
                        'settings' => [
                            'layout' => 'tabs',
                            'show_specifications' => true,
                            'show_reviews' => true,
                        ],
                    ],
                    [
                        'id' => 'related-products',
                        'type' => 'related-products',
                        'settings' => [
                            'heading' => 'You May Also Like',
                            'products_to_show' => 4,
                            'columns' => 4,
                        ],
                    ],
                ],
                'settings' => [
                    'enable_sticky_add_to_cart' => true,
                    'show_recently_viewed' => true,
                ],
            ],
            'product-minimal' => [
                'name' => 'Product - Minimal',
                'slug' => 'product-minimal',
                'description' => 'Minimal product page with essential information only',
                'type' => 'product',
                'route_pattern' => '/products/{slug}',
                'model_binding' => 'Elevate\Product\Models\Product',
                'is_active' => true,
                'sections' => [
                    [
                        'id' => 'product-hero',
                        'type' => 'product-hero',
                        'settings' => [
                            'show_breadcrumbs' => false,
                            'image_position' => 'center',
                            'image_size' => 'medium',
                            'enable_zoom' => false,
                            'show_gallery' => false,
                        ],
                    ],
                    [
                        'id' => 'product-details',
                        'type' => 'product-details',
                        'settings' => [
                            'show_sku' => false,
                            'show_vendor' => false,
                            'show_availability' => true,
                            'show_share_buttons' => false,
                        ],
                    ],
                    [
                        'id' => 'product-description',
                        'type' => 'product-description',
                        'settings' => [
                            'layout' => 'simple',
                            'show_specifications' => false,
                            'show_reviews' => false,
                        ],
                    ],
                ],
                'settings' => [
                    'enable_sticky_add_to_cart' => false,
                    'show_recently_viewed' => false,
                ],
            ],
            'product-sidebar' => [
                'name' => 'Product - Sidebar',
                'slug' => 'product-sidebar',
                'description' => 'Product page with sidebar for additional information',
                'type' => 'product',
                'route_pattern' => '/products/{slug}',
                'model_binding' => 'Elevate\Product\Models\Product',
                'is_active' => true,
                'sections' => [
                    [
                        'id' => 'product-main',
                        'type' => 'product-with-sidebar',
                        'settings' => [
                            'sidebar_position' => 'right',
                            'show_breadcrumbs' => true,
                            'image_size' => 'large',
                            'enable_zoom' => true,
                        ],
                    ],
                    [
                        'id' => 'product-sidebar',
                        'type' => 'sidebar',
                        'blocks' => [
                            [
                                'type' => 'shipping-info',
                                'settings' => [
                                    'title' => 'Shipping Information',
                                    'show_icon' => true,
                                ],
                            ],
                            [
                                'type' => 'trust-badges',
                                'settings' => [
                                    'title' => 'Why Buy From Us',
                                ],
                            ],
                            [
                                'type' => 'categories',
                                'settings' => [
                                    'title' => 'Categories',
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 'product-description',
                        'type' => 'product-description',
                        'settings' => [
                            'layout' => 'accordion',
                            'show_specifications' => true,
                            'show_reviews' => true,
                        ],
                    ],
                ],
                'settings' => [
                    'enable_sticky_add_to_cart' => true,
                    'show_recently_viewed' => true,
                ],
            ],
        ];

        $templatesPath = storage_path("themes/{$theme->slug}/templates");

        foreach ($templates as $slug => $templateData) {
            // Create template in database
            $template = Template::updateOrCreate(
                [
                    'slug' => $slug,
                    'theme_id' => $theme->id,
                ],
                [
                    'name' => $templateData['name'],
                    'description' => $templateData['description'],
                    'type' => 'product',
                    'route_pattern' => $templateData['route_pattern'],
                    'model_binding' => $templateData['model_binding'],
                    'is_active' => $templateData['is_active'],
                    'configuration' => $templateData,
                    'theme_id' => $theme->id,
                ]
            );

            // Write template file
            $filePath = "{$templatesPath}/{$slug}.json";
            $content = json_encode($templateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            File::put($filePath, $content);

            $this->info("  ✓ Created template: {$template->name}");
        }
    }
}
