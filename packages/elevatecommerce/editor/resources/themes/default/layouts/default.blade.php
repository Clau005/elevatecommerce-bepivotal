<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->meta_title ?? $page->title ?? 'Laravel Editor' }}</title>
    <meta name="description" content="{{ $page->meta_description ?? '' }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Theme Styles -->
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
</head>
<body class="live-site">
    <div class="theme-container">
        <div class="theme-content">
            {!! $sectionsHtml !!}
        </div>
    </div>
    
    <!-- Prevent editor styles from affecting live site -->
    <style>
        .live-site section:hover {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }
        
        .live-site .section-hover,
        .live-site .section-selected,
        .live-site .section-item:hover {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        
        /* Remove any potential editor hover effects */
        .live-site *:hover {
            outline: none !important;
        }
        
        .live-site section {
            outline: none !important;
            border: none !important;
        }
    </style>
    
    <!-- Remove any editor-specific JavaScript -->
    <script>
        // Ensure no editor hover effects are applied
        document.addEventListener('DOMContentLoaded', function() {
            // Remove any editor classes that might have been added
            const sections = document.querySelectorAll('section');
            sections.forEach(section => {
                section.classList.remove('section-hover', 'section-selected', 'section-item');
                section.style.outline = 'none';
                section.style.border = 'none';
            });
        });
    </script>
</body>
</html>
