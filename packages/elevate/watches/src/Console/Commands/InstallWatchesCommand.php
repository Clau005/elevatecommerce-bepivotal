<?php

namespace Elevate\Watches\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Elevate\Editor\Models\Theme;
use Elevate\Editor\Models\Template;
use Elevate\Watches\Models\Watch;

class InstallWatchesCommand extends Command
{
    protected $signature = 'watches:install';
    protected $description = 'Install watches package sections and templates';

    public function handle()
    {
        $this->info('Installing Watches package...');

        // 1. Get active theme
        $activeTheme = Theme::where('is_active', true)->first();
        
        if (!$activeTheme) {
            $this->error('No active theme found. Please activate a theme first.');
            return 1;
        }

        $this->info("Active theme: {$activeTheme->name}");

        // 2. Copy sections to active theme
        $this->copySectionsToTheme($activeTheme->slug);

        // 3. Create templates
        $this->createTemplates();

        $this->info('✅ Watches package installed successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('  - Create watches at /admin/watches');
        $this->info('  - Add watches to collections');
        $this->info('  - Customize templates at /admin/templates');

        return 0;
    }

    protected function copySectionsToTheme(string $themeSlug): void
    {
        $this->info('Copying sections to theme...');

        $sourcePath = __DIR__ . '/../../../resources/sections';
        $destinationPath = resource_path("views/themes/{$themeSlug}/sections");

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $sections = ['watch-show', 'watch-grid'];
        
        foreach ($sections as $section) {
            $source = "{$sourcePath}/{$section}";
            $destination = "{$destinationPath}/{$section}";

            if (File::exists($source)) {
                if (File::exists($destination)) {
                    $this->warn("  ⚠ Section '{$section}' already exists, skipping...");
                } else {
                    File::copyDirectory($source, $destination);
                    $this->info("  ✓ Copied section: {$section}");
                }
            }
        }
    }

    protected function createTemplates(): void
    {
        $this->info('Creating templates...');

        // Watch Template
        $watchTemplate = Template::where('model_type', Watch::class)
            ->where('is_default', true)
            ->first();

        if (!$watchTemplate) {
            $templateData = json_decode(
                File::get(__DIR__ . '/../../../resources/templates/default-watch.json'),
                true
            );

            Template::create([
                'name' => $templateData['name'],
                'slug' => 'watch',
                'model_type' => $templateData['model_type'],
                'description' => $templateData['description'],
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Watch Template',
                        'layout' => 'default'
                    ],
                    'sections' => array_map(function ($section) {
                        return [
                            'id' => $section['type'] . '-' . time(),
                            'component' => $section['type'],
                            'data' => $section['configuration']
                        ];
                    }, $templateData['sections'])
                ],
                'draft_configuration' => [
                    'basic_info' => [
                        'title' => 'Watch Template',
                        'layout' => 'default'
                    ],
                    'sections' => array_map(function ($section) {
                        return [
                            'id' => $section['type'] . '-' . time(),
                            'component' => $section['type'],
                            'data' => $section['configuration']
                        ];
                    }, $templateData['sections'])
                ],
                'status' => 'published',
                'is_default' => true,
            ]);

            $this->info('  ✓ Created default watch template');
        } else {
            $this->warn('  ⚠ Watch template already exists');
        }

        // Watch Collection Template (optional, for collections with watches)
        $collectionTemplateData = json_decode(
            File::get(__DIR__ . '/../../../resources/templates/default-watch-collection.json'),
            true
        );

        $watchCollectionTemplate = Template::where('slug', 'watch-collection')->first();

        if (!$watchCollectionTemplate) {
            Template::create([
                'name' => $collectionTemplateData['name'],
                'slug' => 'watch-collection',
                'model_type' => $collectionTemplateData['model_type'],
                'description' => $collectionTemplateData['description'],
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Watch Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => array_map(function ($section) {
                        return [
                            'id' => $section['type'] . '-' . time(),
                            'component' => $section['type'],
                            'data' => $section['configuration']
                        ];
                    }, $collectionTemplateData['sections'])
                ],
                'draft_configuration' => [
                    'basic_info' => [
                        'title' => 'Watch Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => array_map(function ($section) {
                        return [
                            'id' => $section['type'] . '-' . time(),
                            'component' => $section['type'],
                            'data' => $section['configuration']
                        ];
                    }, $collectionTemplateData['sections'])
                ],
                'status' => 'published',
                'is_default' => false,
            ]);

            $this->info('  ✓ Created watch collection template');
        } else {
            $this->warn('  ⚠ Watch collection template already exists');
        }
    }
}
