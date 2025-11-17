@props(['title' => '', 'description' => ''])

@php
    // Get the active theme from config or default to 'default'
    $activeTheme = config('theme.active', 'default');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ? $title . ' - ' : '' }}{{ config('app.name', 'ElevateCommerce') }}</title>
    
    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        .theme-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .theme-content {
            flex: 1;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="theme-container">
        {{-- Include theme header --}}
        @if(View::exists("themes.{$activeTheme}.snippets.header"))
            @include("themes.{$activeTheme}.snippets.header")
        @endif
        
        <div class="theme-content">
            {{ $slot }}
        </div>
        
        {{-- Include theme footer --}}
        @if(View::exists("themes.{$activeTheme}.snippets.footer"))
            @include("themes.{$activeTheme}.snippets.footer")
        @endif
    </div>
    
    @stack('scripts')
</body>
</html>
