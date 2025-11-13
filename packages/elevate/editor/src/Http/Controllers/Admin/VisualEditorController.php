<?php

namespace Elevate\Editor\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Elevate\Editor\Models\Theme;
use Elevate\Editor\Models\Page;
use Elevate\Editor\Models\Template;
use Elevate\Editor\Models\Section;

class VisualEditorController extends Controller
{
    /**
     * Edit a page in the visual editor
     */
    public function editPage(Theme $theme, Page $page)
    {
        // Get available sections for this theme
        $availableSections = Section::where('theme_id', $theme->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($section) {
                return [
                    'slug' => $section->slug,
                    'name' => $section->name,
                    'description' => $section->description,
                    'category' => $section->category,
                    'preview_image' => $section->preview_image,
                    'schema' => $section->schema,
                ];
            })
            ->toArray();

        return view('editor::admin.visual-editor.index', compact(
            'theme',
            'page',
            'availableSections'
        ));
    }

    /**
     * Edit a template in the visual editor
     */
    public function editTemplate(Theme $theme, Template $template)
    {
        // Get available sections for this theme
        $availableSections = Section::where('theme_id', $theme->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($section) {
                return [
                    'slug' => $section->slug,
                    'name' => $section->name,
                    'description' => $section->description,
                    'category' => $section->category,
                    'preview_image' => $section->preview_image,
                    'schema' => $section->schema,
                ];
            })
            ->toArray();

        return view('editor::admin.visual-editor.index', compact(
            'theme',
            'template',
            'availableSections'
        ));
    }
}
