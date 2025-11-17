@extends('core::admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $statsWidgets = \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::getByPosition('stats');
    $mainWidgets = \ElevateCommerce\Core\Support\Dashboard\DashboardRegistry::getByPosition('main');
@endphp

<div class="space-y-4">
    <!-- Page Header -->
    <x-core::heading level="1" subtitle="Welcome back, {{ auth('admin')->user()->first_name }}!">
        Dashboard
    </x-core::heading>

    <!-- Stats Grid -->
    @if(!empty($statsWidgets))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
    <div class="space-y-4">
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
