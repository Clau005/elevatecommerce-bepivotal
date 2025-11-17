<?php

namespace ElevateCommerce\Collections\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Themes\Models\Theme;
use Elevate\Themes\Services\ThemeService;
use Illuminate\Support\Facades\File;

class SyncCollectionTemplatesCommand extends Command
{
    protected $signature = 'collections:sync-templates 
                            {direction=files-to-db : Sync direction: files-to-db or db-to-files}
                            {--force : Force overwrite existing templates}';

    protected $description = 'Sync collection templates between files and database';

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

        $this->info("Syncing collection templates: {$direction}...");

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
                $this->info("✓ Collection templates synced for {$theme->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to sync templates for {$theme->name}: {$e->getMessage()}");
            }
        }

        $this->info('Collection template sync completed!');
        return 0;
    }

    protected function syncFilesToDatabase(Theme $theme, bool $force): void
    {
        $templatesPath = storage_path("themes/{$theme->slug}/templates");

        // Get all collection template files
        $templateFiles = File::glob("{$templatesPath}/collection*.json");

        if (empty($templateFiles)) {
            $this->warn("  No collection template files found");
            $this->info("  Creating default collection templates...");
            $this->createDefaultTemplates($theme->slug);
            $templateFiles = File::glob("{$templatesPath}/collection*.json");
        }

        foreach ($templateFiles as $templatePath) {
            $filename = basename($templatePath);
            
            // Read template file
            $templateContent = File::get($templatePath);
            $templateData = json_decode($templateContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("  Invalid JSON in {$filename}: " . json_last_error_msg());
                continue;
            }

            // Extract template metadata
            $slug = $templateData['slug'] ?? pathinfo($filename, PATHINFO_FILENAME);
            $name = $templateData['name'] ?? ucwords(str_replace('-', ' ', $slug));
            $type = $templateData['type'] ?? 'collection';

            // Check if template already exists
            $existingTemplate = $theme->templates()
                ->where('type', $type)
                ->where('slug', $slug)
                ->first();

            if ($existingTemplate && !$force) {
                $this->warn("  Template '{$name}' already exists. Use --force to overwrite.");
                continue;
            }

            // Remove metadata from configuration
            $configuration = $templateData;
            unset($configuration['name'], $configuration['slug'], $configuration['type']);

            // Create or update template in database
            $template = $theme->templates()->updateOrCreate(
                [
                    'theme_id' => $theme->id,
                    'type' => $type,
                    'slug' => $slug,
                ],
                [
                    'name' => $name,
                    'configuration' => $configuration,
                    'model_binding' => 'ElevateCommerce\Collections\Models\Collection',
                    'route_pattern' => '/collections/{slug}',
                    'is_active' => true,
                ]
            );

            $this->info("  ✓ '{$name}' synced (ID: {$template->id})");
        }
    }

    protected function syncDatabaseToFiles(Theme $theme, bool $force): void
    {
        $templatesPath = storage_path("themes/{$theme->slug}/templates");

        // Create templates directory if it doesn't exist
        if (!File::exists($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
        }

        // Get all collection templates from database for this theme
        $templates = $theme->templates()
            ->where('type', 'collection')
            ->where('is_active', true)
            ->get();

        if ($templates->isEmpty()) {
            $this->warn("  No collection templates found in database");
            return;
        }

        foreach ($templates as $template) {
            $filename = "{$template->slug}.json";
            $filePath = "{$templatesPath}/{$filename}";

            // Check if file exists
            if (File::exists($filePath) && !$force) {
                $this->warn("  File '{$filename}' already exists. Use --force to overwrite.");
                continue;
            }

            // Prepare template data with metadata
            $templateData = [
                'name' => $template->name,
                'slug' => $template->slug,
                'type' => $template->type,
            ];

            // Merge with configuration
            $templateData = array_merge($templateData, $template->configuration);

            // Write to file
            File::put(
                $filePath,
                json_encode($templateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );

            $this->info("  ✓ '{$template->name}' written to {$filename}");
        }
    }

    protected function createDefaultTemplates(string $themeSlug): void
    {
        $templatePath = storage_path("themes/{$themeSlug}/templates");
        
        // Create templates directory if it doesn't exist
        if (!File::exists($templatePath)) {
            File::makeDirectory($templatePath, 0755, true);
        }

        // Template 1: Default Collection
        $defaultTemplate = [
            'name' => 'Default Collection',
            'slug' => 'collection-default',
            'type' => 'collection',
            'meta' => [
                'title' => '{{ collection.name }}',
                'description' => '{{ collection.meta_description ?? collection.description }}',
                'keywords' => [],
            ],
            'sections' => [
                [
                    'id' => 'collection-header',
                    'type' => 'collection-header',
                    'data' => [
                        'showImage' => true,
                        'showName' => true,
                        'showDescription' => true,
                        'image' => '{{ collection.image }}',
                        'name' => '{{ collection.name }}',
                        'description' => '{{ collection.description }}',
                        'backgroundSize' => 'cover',
                        'backgroundPosition' => 'center',
                        'overlay' => [
                            'enabled' => true,
                            'color' => '#000000',
                            'opacity' => 0.3,
                        ],
                        'textAlign' => 'center',
                        'minHeight' => '400px',
                    ],
                ],
                [
                    'id' => 'collection-grid',
                    'type' => 'collection-grid',
                    'data' => [
                        'columns' => 4,
                        'perPage' => 24,
                        'showFilters' => true,
                        'showSort' => true,
                        'showPagination' => true,
                        'gridGap' => '1.5rem',
                        'cardStyle' => 'default',
                        'showQuickView' => true,
                        'showAddToCart' => true,
                        'showPrice' => true,
                        'showComparePrice' => true,
                        'showBadges' => true,
                        'imageAspectRatio' => 'square',
                    ],
                ],
            ],
        ];

        File::put(
            storage_path("themes/{$themeSlug}/templates/collection.json"),
            json_encode($defaultTemplate, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // Copy the other templates from default theme if they exist
        $defaultThemePath = storage_path("themes/default/templates");
        if ($themeSlug !== 'default' && File::exists($defaultThemePath)) {
            $otherTemplates = ['collection-minimal.json', 'collection-sidebar.json'];
            foreach ($otherTemplates as $template) {
                $sourcePath = "{$defaultThemePath}/{$template}";
                $destPath = storage_path("themes/{$themeSlug}/templates/{$template}");
                
                if (File::exists($sourcePath) && !File::exists($destPath)) {
                    File::copy($sourcePath, $destPath);
                }
            }
        }

        $this->info("  Default templates created at: themes/{$themeSlug}/templates/");
    }
}
