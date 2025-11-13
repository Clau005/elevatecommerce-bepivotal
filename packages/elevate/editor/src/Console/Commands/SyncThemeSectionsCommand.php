<?php

namespace Elevate\Editor\Console\Commands;

use Illuminate\Console\Command;
use Elevate\Editor\Models\Theme;
use Elevate\Editor\Jobs\SyncThemeSectionsToDatabase;

class SyncThemeSectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'editor:sync-sections {theme?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync theme sections from files to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $themeSlug = $this->argument('theme');
        $force = $this->option('force');

        if ($themeSlug) {
            // Sync specific theme
            $theme = Theme::where('slug', $themeSlug)->first();

            if (!$theme) {
                $this->error("Theme '{$themeSlug}' not found.");
                return 1;
            }

            $this->info("Syncing sections for theme: {$theme->name}");
            
            $job = new SyncThemeSectionsToDatabase($theme, $force);
            $job->handle();

            $this->info("✓ Sections synced successfully for theme: {$theme->name}");
        } else {
            // Sync all themes
            $themes = Theme::all();

            if ($themes->isEmpty()) {
                $this->warn('No themes found.');
                return 0;
            }

            $this->info("Syncing sections for " . $themes->count() . " theme(s)...");

            foreach ($themes as $theme) {
                $this->line("  → Syncing: {$theme->name}");
                
                $job = new SyncThemeSectionsToDatabase($theme, $force);
                $job->handle();
            }

            $this->info("✓ All theme sections synced successfully!");
        }

        return 0;
    }
}
