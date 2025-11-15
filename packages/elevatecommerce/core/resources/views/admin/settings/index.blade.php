@extends('core::admin.layouts.app')

@section('title', 'Settings')

@section('content')
@php
    $groups = \ElevateCommerce\Core\Support\Settings\SettingsRegistry::getGroups();
@endphp

<div class="space-y-8">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-600">Manage your store settings and configuration</p>
    </div>

    @foreach($groups as $groupName => $pages)
        <!-- Group Section -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4 capitalize">{{ str_replace('_', ' ', $groupName) }}</h2>
            
            <!-- Settings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pages as $key => $page)
                    <a 
                        href="{{ $page['url'] ?? ($page['route'] ? route($page['route']) : '#') }}"
                        class="group relative bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 p-6 border-2 border-transparent hover:border-{{ $page['color'] }}-500"
                    >
                        <!-- Icon -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-{{ $page['color'] }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $page['color'] }}-200 transition-colors">
                                <i class="{{ $page['icon'] }} text-2xl text-{{ $page['color'] }}-600"></i>
                            </div>
                            @if($page['badge'])
                                <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-600 rounded-full">
                                    {{ $page['badge'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Content -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-{{ $page['color'] }}-600 transition-colors">
                            {{ $page['title'] }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $page['description'] }}
                        </p>

                        <!-- Arrow Icon -->
                        <div class="absolute bottom-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-arrow-right text-{{ $page['color'] }}-600"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(empty($groups))
        <div class="text-center py-12">
            <i class="fas fa-cog text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">No settings pages available</p>
        </div>
    @endif
</div>
@endsection
