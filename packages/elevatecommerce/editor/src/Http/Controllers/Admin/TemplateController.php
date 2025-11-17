<?php

namespace ElevateCommerce\Editor\Http\Controllers\Admin;

use ElevateCommerce\Editor\Models\Template;
use ElevateCommerce\Editor\Services\TemplateRegistry;
use ElevateCommerce\Editor\Services\EditorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TemplateController
{
    public function __construct(
        protected TemplateRegistry $registry,
        protected EditorService $editorService
    ) {}

    /**
     * Display a listing of templates
     */
    public function index(Request $request)
    {
        $query = Template::query();

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $templates = $query->latest()->paginate(20);

        $availableModels = $this->registry->getOptions();

        return view('editor::admin.templates.index', compact('templates', 'availableModels'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        $availableModels = $this->registry->getOptions();

        return view('editor::admin.templates.create', compact('availableModels'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:templates,slug',
            'model_type' => 'required|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Initialize with empty configuration matching the standard structure
        $validated['configuration'] = [
            'seo' => [],
            'sections' => [],
            'basic_info' => [
                'layout' => 'default',
            ],
        ];

        $validated['draft_configuration'] = [
            'seo' => [],
            'sections' => [],
            'basic_info' => [
                'layout' => 'default',
            ],
        ];

        $template = Template::create($validated);

        return redirect()
            ->route('admin.visual-editor.templates', ['theme' => 1, 'template' => $template])
            ->with('success', 'Template created! Now build it with the visual editor.');
    }

    /**
     * Display the specified template
     */
    public function show(Template $template)
    {
        return view('editor::admin.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the template
     */
    public function edit(Template $template)
    {
        $availableModels = $this->registry->getOptions();

        return view('editor::admin.templates.edit', compact('template', 'availableModels'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:templates,slug,' . $template->id,
            'model_type' => 'required|string',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Show the visual editor for the template
     */
    public function editVisual(Template $template)
    {
        $configuration = $this->editorService->getEditingConfiguration($template);
        
        // Get available sections from active theme
        $theme = \Elevate\Editor\Models\Theme::active();
        $availableSections = $theme ? $theme->getAvailableSections() : [];

        // Get preview data for this model type
        $previewData = $this->registry->getPreviewData($template->model_type);

        return view('editor::admin.templates.editor', compact(
            'template',
            'configuration',
            'availableSections',
            'previewData'
        ));
    }

    /**
     * Publish the template (move draft to live)
     */
    public function publish(Request $request, Template $template)
    {
        $this->editorService->publish(
            $template,
            auth('admin')->id(),
            $request->input('change_notes')
        );

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template published successfully.');
    }

    /**
     * Set template as default for its model type
     */
    public function setDefault(Template $template)
    {
        $template->setAsDefault();

        return back()->with('success', 'Template set as default.');
    }

    /**
     * Show version history
     */
    public function versions(Template $template)
    {
        $versions = $template->versions()
            ->with('creator')
            ->latest()
            ->paginate(20);

        return view('editor::admin.templates.versions', compact('template', 'versions'));
    }

    /**
     * Restore a specific version
     */
    public function restoreVersion(Template $template, $versionId)
    {
        $version = $template->versions()->findOrFail($versionId);
        $version->restore();

        return back()->with('success', 'Version restored to draft. Review and publish when ready.');
    }
}
