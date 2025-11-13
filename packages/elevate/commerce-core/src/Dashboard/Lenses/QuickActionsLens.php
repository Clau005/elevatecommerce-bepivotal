<?php

namespace Elevate\CommerceCore\Dashboard\Lenses;

use Elevate\CommerceCore\Dashboard\DashboardLens;

class QuickActionsLens extends DashboardLens
{
    public function id(): string
    {
        return 'quick-actions';
    }

    public function name(): string
    {
        return 'Quick Actions';
    }

    public function width(): int
    {
        return 4;
    }

    public function order(): int
    {
        return 201;
    }

    public function data(): array
    {
        $actions = [];
        
        // View Orders
        $actions[] = [
            'label' => 'View Orders',
            'url' => route('admin.orders.index'),
            'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            'color' => 'blue',
        ];
        
        // Add Customer (if route exists)
        if (\Illuminate\Support\Facades\Route::has('admin.customers.create')) {
            $actions[] = [
                'label' => 'Add Customer',
                'url' => route('admin.customers.create'),
                'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                'color' => 'purple',
            ];
        }
        
        // View Customers
        $actions[] = [
            'label' => 'View Customers',
            'url' => route('admin.customers.index'),
            'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            'color' => 'green',
        ];
        
        // Settings
        if (\Illuminate\Support\Facades\Route::has('admin.settings.index')) {
            $actions[] = [
                'label' => 'Settings',
                'url' => route('admin.settings.index'),
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'color' => 'indigo',
            ];
        }
        
        return [
            'actions' => $actions,
        ];
    }

    public function view(): ?string
    {
        return 'commerce::dashboard.lenses.quick-actions';
    }

    public function render(): string
    {
        $data = $this->data();
        $html = '<div class="grid grid-cols-1 gap-3">';
        
        foreach ($data['actions'] as $action) {
            $colorClasses = [
                'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100',
                'green' => 'bg-green-50 text-green-700 hover:bg-green-100',
                'purple' => 'bg-purple-50 text-purple-700 hover:bg-purple-100',
                'indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
            ];
            $colorClass = $colorClasses[$action['color']] ?? 'bg-gray-50 text-gray-700 hover:bg-gray-100';
            
            $html .= "
                <a href='{$action['url']}' class='flex items-center gap-3 p-3 rounded-lg {$colorClass} transition-colors'>
                    <svg class='w-5 h-5 flex-shrink-0' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='{$action['icon']}'/>
                    </svg>
                    <span class='font-medium text-sm'>{$action['label']}</span>
                </a>
            ";
        }
        
        $html .= '</div>';
        return $html;
    }
}
