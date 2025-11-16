<?php

namespace ElevateCommerce\Editor\Http\Controllers\Admin;

use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Template;
use ElevateCommerce\Editor\Models\Section;
use ElevateCommerce\Editor\Jobs\SyncThemeSectionsToDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ThemeController
{
    /**
     * Display a listing of themes
     */
    public function index()
    {
        $themes = Theme::latest()->get();

        return view('editor::admin.themes.index', compact('themes'));
    }

    /**
     * Show the form for creating a new theme
     */
    public function create()
    {
        return view('editor::admin.themes.create');
    }

    /**
     * Store a newly created theme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:themes,slug',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'author_url' => 'nullable|url',
            'preview_image' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $theme = Theme::create($validated);

        // 1. Copy theme files from resources/general to resources/themes/{slug}
        $this->copyThemeFiles($theme->slug);

        // 2. Sync sections from theme files to database (with proper schema)
        $syncJob = new SyncThemeSectionsToDatabase($theme);
        $syncJob->handle();

        // 3. Create default pages for this theme
        $this->createDefaultPages($theme);

        // 4. Create default templates (if they don't exist)
        $this->createDefaultTemplates();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Theme '{$theme->name}' created successfully with default pages, templates, and sections.");
    }

    /**
     * Copy theme files from editor package to resources/views/themes/{slug}
     */
    protected function copyThemeFiles(string $slug): void
    {
        $sourcePath = base_path('packages/elevatecommerce/editor/resources/themes/default');
        $destinationPath = resource_path("views/themes/{$slug}");

        // Create theme directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // Copy sections, snippets, and layouts
        foreach (['sections', 'snippets', 'layouts'] as $folder) {
            $source = "{$sourcePath}/{$folder}";
            $destination = "{$destinationPath}/{$folder}";

            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
            }
        }
    }

    /**
     * Create default pages for the theme
     */
    protected function createDefaultPages(Theme $theme): void
    {
        // Homepage with sections
        Page::create([
            'theme_id' => $theme->id,
            'title' => $theme->name . ' - Homepage',
            'slug' => 'homepage',
            'excerpt' => 'Welcome to your homepage',
            'meta_title' => $theme->name . ' - Home',
            'meta_description' => 'Welcome to ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => $theme->name . ' - Homepage',
                    'slug' => 'homepage',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => $theme->name . ' - Build Beautiful Websites',
                    'meta_description' => 'Create stunning websites with our powerful editor.'
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ' . $theme->name,
                            'subtitle' => 'Create stunning pages with our drag-and-drop editor',
                            'button_text' => 'Get Started',
                            'button_url' => '#',
                            'background_color' => '#1f2937',
                            'text_color' => '#ffffff',
                            'height' => 500
                        ]
                    ],
                    [
                        'id' => 'features-' . (time() + 1),
                        'component' => 'features',
                        'data' => [
                            'title' => 'Powerful Features',
                            'subtitle' => 'Everything you need to build amazing websites',
                            'features' => [
                                [
                                    'title' => 'Drag & Drop',
                                    'description' => 'Easily arrange components with our intuitive interface',
                                    'icon' => 'drag'
                                ],
                                [
                                    'title' => 'Live Preview',
                                    'description' => 'See your changes in real-time as you edit',
                                    'icon' => 'eye'
                                ],
                                [
                                    'title' => 'Responsive Design',
                                    'description' => 'Your sites look great on all devices',
                                    'icon' => 'device'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'draft_configuration' =>  [
                'basic_info' => [
                    'title' => $theme->name . ' - Homepage',
                    'slug' => 'homepage',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => $theme->name . ' - Build Beautiful Websites',
                    'meta_description' => 'Create stunning websites with our powerful editor.'
                ],
                'sections' => [
                    [
                        'id' => 'hero-' . time(),
                        'component' => 'hero',
                        'data' => [
                            'title' => 'Welcome to ' . $theme->name,
                            'subtitle' => 'Create stunning pages with our drag-and-drop editor',
                            'button_text' => 'Get Started',
                            'button_url' => '#',
                            'background_color' => '#1f2937',
                            'text_color' => '#ffffff',
                            'height' => 500
                        ]
                    ],
                    [
                        'id' => 'features-' . (time() + 1),
                        'component' => 'features',
                        'data' => [
                            'title' => 'Powerful Features',
                            'subtitle' => 'Everything you need to build amazing websites',
                            'features' => [
                                [
                                    'title' => 'Drag & Drop',
                                    'description' => 'Easily arrange components with our intuitive interface',
                                    'icon' => 'drag'
                                ],
                                [
                                    'title' => 'Live Preview',
                                    'description' => 'See your changes in real-time as you edit',
                                    'icon' => 'eye'
                                ],
                                [
                                    'title' => 'Responsive Design',
                                    'description' => 'Your sites look great on all devices',
                                    'icon' => 'device'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'status' => 'draft',
            'is_active' => false,
        ]);

        // About page
        Page::create([
            'theme_id' => $theme->id,
            'title' => 'About Us',
            'slug' => 'about',
            'excerpt' => 'Learn more about us',
            'meta_title' => 'About Us - ' . $theme->name,
            'meta_description' => 'Learn more about ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => 'About Us',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About Us - ' . $theme->name,
                    'meta_description' => 'Learn more about ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'About Us',
                            'content' => '<p>Welcome to our about page. Tell your story here.</p>'
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'About Us',
                    'slug' => 'about',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'About Us - ' . $theme->name,
                    'meta_description' => 'Learn more about ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'About Us',
                            'content' => '<p>Welcome to our about page. Tell your story here.</p>'
                        ]
                    ]
                ]
            ],
            'status' => 'draft',
            'is_active' => false,
        ]);

        // Contact page
        Page::create([
            'theme_id' => $theme->id,
            'title' => 'Contact Us',
            'slug' => 'contact',
            'excerpt' => 'Get in touch with us',
            'meta_title' => 'Contact Us - ' . $theme->name,
            'meta_description' => 'Contact ' . $theme->name,
            'configuration' => [
                'basic_info' => [
                    'title' => 'Contact Us',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact Us - ' . $theme->name,
                    'meta_description' => 'Contact ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'Get In Touch',
                            'content' => '<p>We\'d love to hear from you. Contact us today!</p>'
                        ]
                    ]
                ]
            ],
            'draft_configuration' => [
                'basic_info' => [
                    'title' => 'Contact Us',
                    'slug' => 'contact',
                    'layout' => 'default'
                ],
                'seo' => [
                    'meta_title' => 'Contact Us - ' . $theme->name,
                    'meta_description' => 'Contact ' . $theme->name
                ],
                'sections' => [
                    [
                        'id' => 'text-' . time(),
                        'component' => 'text',
                        'data' => [
                            'title' => 'Get In Touch',
                            'content' => '<p>We\'d love to hear from you. Contact us today!</p>'
                        ]
                    ]
                ]
            ],
            'status' => 'draft',
            'is_active' => false,
        ]);
    }

    /**
     * Create default templates if they don't exist
     */
    protected function createDefaultTemplates(): void
    {
        // Collection Template
        Template::firstOrCreate(
            ['slug' => 'collection'],
            [
                'name' => 'Collection Template',
                'model_type' => 'Elevate\\Collections\\Models\\Collection',
                'description' => 'Default template for collection pages',
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'collection-hero-' . time(),
                            'component' => 'collection-hero',
                            'data' => [
                                'title' => '{{ model.name }}',
                                'subtitle' => '{{ model.description }}',
                                'show_product_count' => true,
                                'background_color' => '#1f2937',
                                'text_color' => '#ffffff',
                                'height' => 400
                            ]
                        ],
                        [
                            'id' => 'collection-grid-' . (time() + 1),
                            'component' => 'collection-grid',
                            'data' => [
                                'columns' => 4,
                                'gap' => '2rem',
                                'show_filters' => true,
                                'show_sort' => true
                            ]
                        ]
                    ]
                ],
                'draft_configuration' => [
                    'basic_info' => [
                        'title' => 'Collection Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'collection-hero-' . time(),
                            'component' => 'collection-hero',
                            'data' => [
                                'title' => '{{ model.name }}',
                                'subtitle' => '{{ model.description }}',
                                'show_product_count' => true,
                                'background_color' => '#1f2937',
                                'text_color' => '#ffffff',
                                'height' => 400
                            ]
                        ],
                        [
                            'id' => 'collection-grid-' . (time() + 1),
                            'component' => 'collection-grid',
                            'data' => [
                                'columns' => 4,
                                'gap' => '2rem',
                                'show_filters' => true,
                                'show_sort' => true
                            ]
                        ]
                    ]
                ],
                'status' => 'draft',
                'is_default' => true,
            ]
        );

        // Product Template
        Template::firstOrCreate(
            ['slug' => 'product'],
            [
                'name' => 'Product Template',
                'model_type' => 'Elevate\\Product\\Models\\Product',
                'description' => 'Default template for product pages',
                'configuration' => [
                    'basic_info' => [
                        'title' => 'Product Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'product-show-' . time(),
                            'component' => 'product-show',
                            'data' => [
                                'layout' => 'default',
                                'show_breadcrumbs' => true,
                                'show_related_products' => true,
                                'gallery_style' => 'thumbnails'
                            ]
                        ]
                    ]
                ],
                'draft_configuration' =>  [
                    'basic_info' => [
                        'title' => 'Product Template',
                        'layout' => 'default'
                    ],
                    'sections' => [
                        [
                            'id' => 'product-show-' . time(),
                            'component' => 'product-show',
                            'data' => [
                                'layout' => 'default',
                                'show_breadcrumbs' => true,
                                'show_related_products' => true,
                                'gallery_style' => 'thumbnails'
                            ]
                        ]
                    ]
                ],
                'status' => 'draft',
                'is_default' => true,
            ]
        );
    }

    /**
     * Display the specified theme
     */
    public function show(Theme $theme)
    {
        $sections = $theme->getAvailableSections();
        $hasLayout = $theme->hasLayout('default');

        return view('editor::admin.themes.show', compact('theme', 'sections', 'hasLayout'));
    }

    /**
     * Show the form for editing the theme
     */
    public function edit(Theme $theme)
    {
        return view('editor::admin.themes.edit', compact('theme'));
    }

    /**
     * Update the specified theme
     */
    public function update(Request $request, Theme $theme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:themes,slug,' . $theme->id,
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'author_url' => 'nullable|url',
            'preview_image' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        $theme->update($validated);

        return redirect()
            ->route('admin.themes.index')
            ->with('success', 'Theme updated successfully.');
    }

    /**
     * Remove the specified theme
     */
    public function destroy(Theme $theme)
    {
        if ($theme->is_active) {
            return back()->with('error', 'Cannot delete the active theme.');
        }

        $theme->delete();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', 'Theme deleted successfully.');
    }

    /**
     * Activate a theme
     */
    public function activate(Theme $theme)
    {
        $theme->activate();

        return redirect()
            ->route('admin.themes.index')
            ->with('success', "Theme '{$theme->name}' is now active.");
    }
}
