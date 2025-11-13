<?php

namespace Elevate\Collections\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\Collections\Models\CollectionFilter;
use Elevate\Collections\Models\CollectionFilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilterController extends Controller
{
    /**
     * Display a listing of filters
     */
    public function index()
    {
        $filters = CollectionFilter::with(['collections', 'values'])
            ->orderBy('sort_order')
            ->paginate(20);

        return view('collections::admin.filters.index', compact('filters'));
    }

    /**
     * Show the form for creating a new filter
     */
    public function create()
    {
        return view('collections::admin.filters.create');
    }

    /**
     * Store a newly created filter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collection_filters,slug',
            'type' => 'required|in:select,range,checkbox',
            'source_model' => 'required|string',
            'source_column' => 'required|string',
            'source_relation' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $filter = CollectionFilter::create($validated);

        return redirect()
            ->route('admin.filters.edit', $filter)
            ->with('success', 'Filter created successfully. Now add filter values and assign to collections.');
    }

    /**
     * Show the form for editing a filter
     */
    public function edit(CollectionFilter $filter)
    {
        $filter->load(['collections', 'values']);
        
        return view('collections::admin.filters.edit', compact('filter'));
    }

    /**
     * Update the specified filter
     */
    public function update(Request $request, CollectionFilter $filter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'type' => 'required|in:select,range,checkbox',
            'source_model' => 'required|string',
            'source_column' => 'required|string',
            'source_relation' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
            'config' => 'nullable|array',
        ]);

        $filter->update($validated);

        return redirect()
            ->route('admin.filters.edit', $filter)
            ->with('success', 'Filter updated successfully.');
    }

    /**
     * Remove the specified filter
     */
    public function destroy(CollectionFilter $filter)
    {
        $filter->delete();

        return redirect()
            ->route('admin.filters.index')
            ->with('success', 'Filter deleted successfully.');
    }

    /**
     * Auto-discover and sync filter values from database
     */
    public function syncValues(Request $request, CollectionFilter $filter)
    {
        $model = $filter->source_model;
        $column = $filter->source_column;
        
        if (!class_exists($model)) {
            return back()->with('error', 'Model class not found.');
        }

        // Get distinct values from the database
        $query = $model::query();
        
        if ($filter->source_relation) {
            // If filtering by relation, get values from related model
            $query->with($filter->source_relation);
            $values = $query->get()
                ->pluck($filter->source_relation . '.' . $column)
                ->unique()
                ->filter()
                ->values();
        } else {
            // Direct column values
            $values = $query->distinct()
                ->pluck($column)
                ->filter()
                ->values();
        }

        // Create or update filter values
        foreach ($values as $value) {
            $slug = Str::slug($value);
            $label = ucfirst($value);
            
            CollectionFilterValue::updateOrCreate(
                [
                    'filter_id' => $filter->id,
                    'slug' => $slug,
                ],
                [
                    'label' => $label,
                    'value' => $value,
                    'is_active' => true,
                ]
            );
        }

        return back()->with('success', count($values) . ' filter values synced successfully.');
    }
}
