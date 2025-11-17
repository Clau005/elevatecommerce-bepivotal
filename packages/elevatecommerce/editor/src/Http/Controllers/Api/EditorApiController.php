<?php

namespace ElevateCommerce\Editor\Http\Controllers\Api;

use ElevateCommerce\Editor\Models\Template;
use ElevateCommerce\Editor\Models\Page;
use ElevateCommerce\Editor\Models\Theme;
use ElevateCommerce\Editor\Models\EditorSession;
use ElevateCommerce\Editor\Services\EditorService;
use ElevateCommerce\Editor\Services\TemplateRegistry;
use ElevateCommerce\Editor\Services\RenderEngine;
use Illuminate\Http\Request;

class EditorApiController
{
    public function __construct(
        protected EditorService $editorService,
        protected TemplateRegistry $registry,
        protected RenderEngine $renderEngine
    ) {}

    /**
     * Create or update an editor session
     */
    public function createSession(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'configuration' => 'required|array',
        ]);

        $template = Template::findOrFail($validated['template_id']);
        
        $session = $this->editorService->createSession(
            $template,
            auth('admin')->id(),
            $validated['configuration']
        );

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    /**
     * Update an editor session
     */
    public function updateSession(Request $request, EditorSession $session)
    {
        $validated = $request->validate([
            'configuration' => 'required|array',
        ]);

        $session->update([
            'configuration' => $validated['configuration'],
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    /**
     * Save draft configuration (unified for pages and templates)
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:page,template',
            'id' => 'required|integer',
            'configuration' => 'required|array',
        ]);

        if ($validated['type'] === 'page') {
            $page = Page::findOrFail($validated['id']);
            $page->update(['draft_configuration' => $validated['configuration']]);
        } else {
            $template = Template::findOrFail($validated['id']);
            $template->update(['draft_configuration' => $validated['configuration']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully',
        ]);
    }

    /**
     * Update preview configuration in session (for real-time updates)
     */
    public function updatePreview(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:page,template',
            'id' => 'required|integer',
            'configuration' => 'required|array',
        ]);

        if ($validated['type'] === 'page') {
            session(['editor.preview.page.' . $validated['id'] => $validated['configuration']]);
        } else {
            session(['editor.preview.template.' . $validated['id'] => $validated['configuration']]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Preview a template with configuration
     */
    public function preview(Request $request, Template $template)
    {
        $validated = $request->validate([
            'configuration' => 'required|array',
        ]);

        // Temporarily set draft configuration
        $template->draft_configuration = $validated['configuration'];

        // Get preview data
        $model = $this->registry->getPreviewData($template->model_type);

        if (!$model) {
            return response()->json([
                'error' => 'No preview data available for this model type',
            ], 404);
        }

        // Render with draft configuration
        $html = $this->renderEngine->renderTemplate($template, $model, true);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Preview a page with configuration
     */
    public function previewPage(Request $request, Page $page)
    {
        $validated = $request->validate([
            'configuration' => 'required|array',
        ]);

        // Temporarily set draft configuration
        $page->draft_configuration = $validated['configuration'];

        // Render with draft configuration
        $html = $this->renderEngine->renderPage($page, true);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Get available sections for a theme
     */
    public function getSections(Theme $theme)
    {
        $sections = $theme->getAvailableSections();

        return response()->json([
            'success' => true,
            'sections' => $sections,
        ]);
    }

    /**
     * Get schema for a specific section
     */
    public function getSectionSchema(Request $request, string $slug)
    {
        $theme = Theme::active();
        
        if (!$theme) {
            return response()->json(['error' => 'No active theme'], 404);
        }

        $schemaPath = $theme->path . '/sections/' . $slug . '/schema.json';

        if (!file_exists($schemaPath)) {
            return response()->json(['error' => 'Section not found'], 404);
        }

        $schema = json_decode(file_get_contents($schemaPath), true);

        return response()->json([
            'success' => true,
            'schema' => $schema,
        ]);
    }

    /**
     * Get preview data for a model type
     */
    public function getPreviewData(string $modelType)
    {
        $model = $this->registry->getPreviewData($modelType);

        if (!$model) {
            return response()->json([
                'error' => 'No preview data available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $model->getTemplateData(),
        ]);
    }

    /**
     * Get active editors for a template (for collaboration warnings)
     */
    public function getActiveEditors(Template $template)
    {
        $editors = $this->editorService->getActiveEditors($template);

        return response()->json([
            'success' => true,
            'editors' => $editors->map(function ($session) {
                return [
                    'user_id' => $session->user_id,
                    'user_name' => $session->user->name ?? 'Unknown',
                    'last_activity' => $session->last_activity_at->diffForHumans(),
                ];
            }),
        ]);
    }
    
    /**
     * Get pages for a theme
     */
    public function getThemePages(Theme $theme)
    {
        $pages = Page::where('theme_id', $theme->id)
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'pages' => $pages,
        ]);
    }
    
    /**
     * Get all templates (global, not theme-specific)
     */
    public function getAllTemplates()
    {
        $templates = Template::select('id', 'name', 'slug', 'model_type')
            ->orderBy('name')
            ->get()
            ->map(function ($template) {
                // Check if this template has any model instances using it
                $instanceCount = 0;
                $hasInstances = false;
                
                if ($template->model_type && class_exists($template->model_type)) {
                    $instanceCount = $template->model_type::where('template_id', $template->id)->count();
                    $hasInstances = $instanceCount > 0;
                }
                
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'slug' => $template->slug,
                    'model_type' => $template->model_type,
                    'instance_count' => $instanceCount,
                    'has_instances' => $hasInstances,
                ];
            });

        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }
}
