@extends('core::admin.layouts.app')

@section('title', 'Settings')

@section('content')
@php
    $groups = \ElevateCommerce\Core\Support\Settings\SettingsRegistry::getGroups();
@endphp

<div class="space-y-6">
    <!-- Page Header -->
    <x-core::heading level="1" subtitle="Manage your store settings and configuration">
        Settings
    </x-core::heading>

    @foreach($groups as $groupName => $pages)
        <!-- Group Section -->
        <div>
            <x-core::heading level="2" class="mb-3 capitalize">
                {{ str_replace('_', ' ', $groupName) }}
            </x-core::heading>
            
            <!-- Settings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pages as $key => $page)
                    <a 
                        href="{{ $page['url'] ?? ($page['route'] ? route($page['route']) : '#') }}"
                        class="group relative bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 p-4 border-2 border-transparent hover:border-{{ $page['color'] }}-500"
                    >
                        <!-- Icon -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-{{ $page['color'] }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $page['color'] }}-200 transition-colors">
                                <i class="{{ $page['icon'] }} text-lg text-{{ $page['color'] }}-600"></i>
                            </div>
                            @if($page['badge'])
                                <x-core::badge variant="danger" size="sm">
                                    {{ $page['badge'] }}
                                </x-core::badge>
                            @endif
                        </div>

                        <!-- Content -->
                        <h3 class="text-sm font-semibold text-gray-900 mb-1 group-hover:text-{{ $page['color'] }}-600 transition-colors">
                            {{ $page['title'] }}
                        </h3>
                        <p class="text-xs text-gray-600">
                            {{ $page['description'] }}
                        </p>

                        <!-- Arrow Icon -->
                        <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-arrow-right text-sm text-{{ $page['color'] }}-600"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(empty($groups))
        <div class="text-center py-8">
            <i class="fas fa-cog text-3xl text-gray-400 mb-3"></i>
            <p class="text-sm text-gray-600">No settings pages available</p>
        </div>
    @endif
</div>
@endsection
