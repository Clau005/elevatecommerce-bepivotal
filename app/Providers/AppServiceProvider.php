<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ElevateCommerce\Collections\Services\CollectableRegistry;
use App\Models\TestingPurchasable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register TestingPurchasable as a collectable type
        if ($this->app->bound(CollectableRegistry::class)) {
            $registry = $this->app->make(CollectableRegistry::class);
            
            $registry->register(TestingPurchasable::class, [
                'label' => 'Testing Purchasables',
                'singular' => 'Testing Purchasable',
                'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', // Beaker/flask SVG path
            ]);
        }
        
        // Register admin navigation
        $this->registerNavigation();
    }
    
    /**
     * Register admin navigation items
     */
    protected function registerNavigation(): void
    {
        \ElevateCommerce\Core\Support\Navigation\NavigationRegistry::register('admin', [
            'label' => 'Testing Products',
            'route' => 'admin.testing-purchasables.index',
            'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'order' => 50,
        ]);
    }
}
