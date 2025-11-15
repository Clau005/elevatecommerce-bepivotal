@extends('core::admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $statsWidgets = \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::getByPosition('stats');
    $mainWidgets = \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::getByPosition('main');
@endphp

<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back, {{ auth('admin')->user()->first_name }}!</p>
    </div>

    <!-- Stats Grid -->
    @if(!empty($statsWidgets))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($statsWidgets as $key => $widget)
            @if($widget['view'])
                @include($widget['view'], $widget['data'])
            @elseif($widget['component'])
                <x-dynamic-component :component="$widget['component']" :data="$widget['data']" />
            @endif
        @endforeach
    </div>
    @endif

    <!-- Main Widgets -->
    @if(!empty($mainWidgets))
    <div class="space-y-6">
        @foreach($mainWidgets as $key => $widget)
            @if($widget['view'])
                @include($widget['view'], $widget['data'])
            @elseif($widget['component'])
                <x-dynamic-component :component="$widget['component']" :data="$widget['data']" />
            @endif
        @endforeach
    </div>
    @endif
</div>
@endsection
