<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Language;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages.
     */
    public function index(Request $request): View
    {
        $query = Language::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('enabled')) {
            $query->where('is_enabled', true);
        }
        
        $languages = $query->orderBy('is_default', 'desc')
                          ->orderBy('name')
                          ->paginate(15);
        
        // Prepare data array
        $tableData = $languages->map(function($language) {
            return [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'enabled' => $language->is_enabled,
                'default' => $language->is_default,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'code' => [
                'label' => 'Code',
                'sortable' => true,
            ],
            'name' => [
                'label' => 'Name',
                'sortable' => true,
            ],
            'enabled' => [
                'label' => 'Status',
                'sortable' => false,
                'render' => function($row) {
                    return $row['enabled']
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Enabled</span>'
                        : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Disabled</span>';
                }
            ],
            'default' => [
                'label' => 'Default',
                'sortable' => false,
                'render' => function($row) {
                    return $row['default']
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Default</span>'
                        : '';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $editUrl = route('admin.settings.languages.edit', $row['id']);
                    
                    $html = '<div class="flex items-center gap-2">';
                    $html .= '<a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">Edit</a>';
                    
                    if (!$row['default']) {
                        $html .= '<button onclick="confirmDelete(\''.$row['id'].'\')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Delete</button>';
                    }
                    
                    $html .= '</div>';
                    return $html;
                }
            ],
        ];
        
        return view('commerce::admin.settings.languages.index', [
            'languages' => $languages,
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }

    /**
     * Show the form for creating a new language.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.languages.form', [
            'language' => new Language(),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created language.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code',
            'name' => 'required|string|max:255',
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($validated);

        return redirect()->route('admin.settings.languages.index')
                        ->with('success', 'Language created successfully.');
    }

    /**
     * Show the form for editing a language.
     */
    public function edit(Language $language): View
    {
        return view('commerce::admin.settings.languages.form', [
            'language' => $language,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified language.
     */
    public function update(Request $request, Language $language)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'name' => 'required|string|max:255',
            'is_enabled' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            Language::where('id', '!=', $language->id)
                   ->where('is_default', true)
                   ->update(['is_default' => false]);
        }

        $language->update($validated);

        return redirect()->route('admin.settings.languages.index')
                        ->with('success', 'Language updated successfully.');
    }

    /**
     * Remove the specified language.
     */
    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete the default language.');
        }

        $language->delete();

        return redirect()->route('admin.settings.languages.index')
                        ->with('success', 'Language deleted successfully.');
    }
}
