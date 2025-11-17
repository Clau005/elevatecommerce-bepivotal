<?php

namespace ElevateCommerce\Editor\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Models\Section;

class SyncThemeSectionsToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Theme $theme;
    protected bool $force;

    /**
     * Create a new job instance.
     */
    public function __construct(Theme $theme, bool $force = false)
    {
        $this->theme = $theme;
        $this->force = $force;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting sections sync for theme: {$this->theme->name}");

        $sectionsPath = resource_path("views/themes/{$this->theme->slug}/sections");

        if (!File::exists($sectionsPath)) {
            Log::warning("No sections directory found for theme {$this->theme->slug}");
            return;
        }

        try {
            $sectionDirs = File::directories($sectionsPath);

            foreach ($sectionDirs as $sectionDir) {
                $sectionSlug = basename($sectionDir);
                $this->syncSection($sectionDir, $sectionSlug);
            }

            Log::info("Successfully synced " . count($sectionDirs) . " sections for theme {$this->theme->name}");

        } catch (\Exception $e) {
            Log::error("Failed to sync sections for theme {$this->theme->slug}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync individual section
     */
    protected function syncSection(string $sectionDir, string $sectionSlug): void
    {
        $configPath = $sectionDir . '/configuration.json';
        $templatePath = $sectionDir . '/index.blade.php';

        if (!File::exists($configPath) || !File::exists($templatePath)) {
            Log::warning("Missing files for section {$sectionSlug}, skipping");
            return;
        }

        // Read configuration
        $config = json_decode(File::get($configPath), true);
        if (!$config) {
            Log::warning("Invalid configuration for section {$sectionSlug}, skipping");
            return;
        }

        // Read template
        $bladeCode = File::get($templatePath);

        // Determine category from config or slug
        $category = $config['category'] ?? 'general';
        if (str_contains($sectionSlug, 'collection-')) {
            $category = 'collection';
        } elseif (str_contains($sectionSlug, 'product-')) {
            $category = 'product';
        }

        // Create or update section
        $section = Section::updateOrCreate(
            [
                'theme_id' => $this->theme->id,
                'slug' => $sectionSlug,
            ],
            [
                'name' => $config['name'] ?? ucfirst(str_replace('-', ' ', $sectionSlug)),
                'description' => $config['description'] ?? '',
                'category' => $category,
                'blade_code' => $bladeCode,
                'schema' => [
                    'name' => $config['name'] ?? ucfirst(str_replace('-', ' ', $sectionSlug)),
                    'description' => $config['description'] ?? '',
                    'category' => $category,
                    'fields' => $config['fields'] ?? [],
                ],
                'preview_image' => $config['preview_image'] ?? null,
                'is_active' => true,
            ]
        );

        Log::info("Synced section: {$section->name}");
    }
}
