<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Themes Path
    |--------------------------------------------------------------------------
    |
    | The path where theme files are stored (layouts, sections, snippets).
    | Relative to resources/views/
    |
    */
    'themes_path' => resource_path('views/themes'),

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The slug of the default theme to use if no theme is active.
    |
    */
    'default_theme' => env('EDITOR_DEFAULT_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) to cache rendered templates and sections.
    | Set to 0 to disable caching.
    |
    */
    'cache_duration' => env('EDITOR_CACHE_DURATION', 3600),

    /*
    |--------------------------------------------------------------------------
    | Editor Auto-save Interval
    |--------------------------------------------------------------------------
    |
    | How often (in seconds) the editor should auto-save drafts.
    |
    */
    'autosave_interval' => 30,

    /*
    |--------------------------------------------------------------------------
    | Session Timeout
    |--------------------------------------------------------------------------
    |
    | How long (in minutes) before an editor session is considered inactive.
    |
    */
    'session_timeout' => 5,

    /*
    |--------------------------------------------------------------------------
    | Version History Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of versions to keep per template.
    | Set to 0 for unlimited.
    |
    */
    'version_limit' => 50,

    /*
    |--------------------------------------------------------------------------
    | Preview Breakpoints
    |--------------------------------------------------------------------------
    |
    | Responsive breakpoints for the editor preview.
    |
    */
    'breakpoints' => [
        'mobile' => 375,
        'tablet' => 768,
        'desktop' => 1440,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Section Types
    |--------------------------------------------------------------------------
    |
    | Categories for organizing sections in the editor.
    |
    */
    'section_categories' => [
        'hero' => 'Hero Sections',
        'content' => 'Content',
        'products' => 'Products',
        'collections' => 'Collections',
        'testimonials' => 'Testimonials',
        'cta' => 'Call to Action',
        'footer' => 'Footer',
        'general' => 'General',
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Upload Settings
    |--------------------------------------------------------------------------
    |
    | Settings for image uploads in the editor.
    |
    */
    'images' => [
        'disk' => env('EDITOR_IMAGES_DISK', 'public'),
        'path' => 'editor/images',
        'max_size' => 5120, // KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults
    |--------------------------------------------------------------------------
    |
    | Default SEO settings for pages and templates.
    |
    */
    'seo' => [
        'meta_title_suffix' => env('APP_NAME', 'Laravel'),
        'meta_description_length' => 160,
        'og_image_default' => null,
    ],
];
