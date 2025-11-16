<?php

namespace ElevateCommerce\Editor\Http\Controllers\Admin;

use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Services\EditorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController
{
    public function __construct(
        protected EditorService $editorService
    ) {}

    /**
     * Display a listing of pages
     */
    public function index(Request $request)
    {
        $query = Page::with('theme');

        // Filter by theme
        if ($request->filled('theme_id')) {
            $query->where('theme_id', $request->theme_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->latest()->paginate(20);
        $themes = Theme::all();

        return view('editor::admin.pages.index', compact('pages', 'themes'));
    }

    /**
     * Show the form for creating a new page
     */
    public function create()
    {
        $themes = Theme::all();
        $activeTheme = Theme::active();

        return view('editor::admin.pages.create', compact('themes', 'activeTheme'));
    }

    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'theme_id' => 'required|exists:themes,id',
            'excerpt' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Initialize with empty configuration
        $validated['configuration'] = [
            'layout' => 'default',
            'sections' => [],
        ];

        $validated['draft_configuration'] = [
            'layout' => 'default',
            'sections' => [],
        ];

        $page = Page::create($validated);

        return redirect()
            ->route('admin.visual-editor.pages', ['theme' => $page->theme_id ?? 1, 'page' => $page])
            ->with('success', 'Page created! Now build it with the visual editor.');
    }

    /**
     * Display the specified page
     */
    public function show(Page $page)
    {
        return view('editor::admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the page
     */
    public function edit(Page $page)
    {
        $themes = Theme::all();

        return view('editor::admin.pages.edit', compact('page', 'themes'));
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'theme_id' => 'required|exists:themes,id',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page->update($validated);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    /**
     * Show the visual editor for the page
     */
    public function editVisual(Page $page)
    {
        $configuration = $this->editorService->getEditingConfiguration($page);
        
        // Get available sections from page's theme
        $availableSections = $page->theme->getAvailableSections();

        return view('editor::admin.pages.editor', compact(
            'page',
            'configuration',
            'availableSections'
        ));
    }

    /**
     * Publish the page (move draft to live)
     */
    public function publish(Request $request, Page $page)
    {
        $this->editorService->publish(
            $page,
            auth('staff')->id()
        );

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page published successfully.');
    }
}
