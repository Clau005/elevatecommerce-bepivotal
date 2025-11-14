<?php

namespace Elevate\Collections\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Elevate\Collections\Models\Collection;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Collection::with(['parent', 'collectables']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } elseif ($request->parent_id) {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortColumns = ['name', 'sort_order', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'sort_order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $perPage = $request->get('per_page', 20);
        $collections = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $collections->appends($request->query());

        // Transform data for table
        $tableData = $collections->map(function($collection) {
            return [
                'id' => $collection->id,
                'name' => $collection->name,
                'slug' => $collection->slug,
                'parent_name' => $collection->parent ? $collection->parent->name : null,
                'parent_id' => $collection->parent_id,
                'items_count' => $collection->collectables->count(),
                'is_active' => $collection->is_active,
                'created_at' => $collection->created_at,
            ];
        })->toArray();

        // Define columns
        $columns = [
            'name' => [
                'label' => 'Name',
                'render' => function($row) {
                    $indent = $row['parent_id'] ? '<svg class="w-4 h-4 text-gray-400 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>' : '';
                    return '
                        <div class="flex items-center">
                            '.$indent.'
                            <div>
                                <div class="text-sm font-medium text-gray-900">'.$row['name'].'</div>
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">'.$row['slug'].'</code>
                            </div>
                        </div>
                    ';
                }
            ],
            'parent_name' => [
                'label' => 'Parent',
                'render' => function($row) {
                    return $row['parent_name'] 
                        ? '<span class="text-sm text-gray-700">'.$row['parent_name'].'</span>'
                        : '<span class="text-xs text-gray-400">Root</span>';
                }
            ],
            'items_count' => [
                'label' => 'Items',
                'render' => function($row) {
                    return '<span class="text-sm text-gray-700">'.$row['items_count'].'</span>';
                }
            ],
            'is_active' => [
                'label' => 'Status',
                'render' => function($row) {
                    $statusClass = $row['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    $statusText = $row['is_active'] ? 'Active' : 'Inactive';
                    return '<span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 '.$statusClass.'">'.$statusText.'</span>';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'class' => 'text-right text-sm font-medium whitespace-nowrap',
                'render' => function($row) {
                    $html = '<div class="flex items-center justify-end gap-2">';
                    
                    // Edit button
                    $html .= '<a href="'.route('admin.collections.edit', $row['id']).'" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                        Edit
                    </a>';
                    
                    // Delete button
                    $html .= '<form action="'.route('admin.collections.destroy', $row['id']).'" method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this collection?\')">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>';
                    
                    $html .= '</div>';
                    return $html;
                }
            ],
        ];

        // Get all root collections for filter
        $parentCollections = Collection::whereNull('parent_id')->orderBy('name')->get();

        return view('collections::admin.collections.index', compact('collections', 'tableData', 'columns', 'parentCollections'));
    }

    public function create()
    {
        $parentCollections = Collection::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $templates = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Collections\Models\Collection')
            ->get();

        


        return view('collections::admin.collections.create', compact('parentCollections', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:collections,id',
            'image' => 'nullable|string',
            'template_id' => 'required|exists:templates,id',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');
        
        // Convert empty string to null for template_id
        if (isset($validated['template_id']) && $validated['template_id'] === '') {
            $validated['template_id'] = null;
        }

        $collection = Collection::create($validated);

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', 'Collection created successfully.');
    }

    public function edit(Collection $collection)
    {
        $parentCollections = Collection::whereNull('parent_id')
            ->where('id', '!=', $collection->id)
            ->orderBy('name')
            ->get();

        $templates = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Collections\Models\Collection')
            ->get();

        $collectableRegistry = app(\Elevate\Collections\Services\CollectableRegistry::class);
        $collectableTypes = $collectableRegistry->all();

        // Load relationships
        $collection->load('children');
        
        // Load collectables with error handling for invalid types
        try {
            $collection->load('collectables.collectable');
        } catch (\Exception $e) {
            // If there's an error loading collectables (e.g., invalid class), load without the nested relation
            $collection->load('collectables');
        }

        return view('collections::admin.collections.edit', compact('collection', 'parentCollections', 'templates', 'collectableTypes'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collections,slug,' . $collection->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:collections,id',
            'image' => 'nullable|string',
            'template_id' => 'nullable|exists:templates,id',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'enabled_filters' => 'nullable|array',
            'enabled_filters.*' => 'exists:collection_filters,id',
        ]);

        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');
        
        // Convert empty string to null for template_id
        if (isset($validated['template_id']) && $validated['template_id'] === '') {
            $validated['template_id'] = null;
        }

        $collection->update($validated);

        // Sync filter assignments using pivot table
        if ($request->has('enabled_filters')) {
            $enabledFilterIds = $request->input('enabled_filters', []);
            
            // Sync the many-to-many relationship
            $collection->filters()->sync(
                collect($enabledFilterIds)->mapWithKeys(function ($filterId) {
                    return [$filterId => ['is_active' => true, 'sort_order' => 0]];
                })->toArray()
            );
        } else {
            // If no filters selected, detach all
            $collection->filters()->detach();
        }

        return redirect()
            ->route('admin.collections.edit', $collection)
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()
            ->route('admin.collections.index')
            ->with('success', 'Collection deleted successfully.');
    }

    /**
     * Get available items for a type
     */
    public function getAvailableItems(Request $request)
    {
        $type = $request->get('type');
        
        // Get the full class name from the registry
        $registry = app(\Elevate\Collections\Services\CollectableRegistry::class);
        $modelClass = $registry->getByKey($type);
        
        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json([]);
        }
        
        // Fetch items - try 'name' first, fallback to 'title'
        $items = $modelClass::query()
            ->select('id')
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), 'name'),
                fn($q) => $q->addSelect('name'),
                fn($q) => $q->addSelect('title as name')
            )
            ->get();
        
        return response()->json($items);
    }

    /**
     * Get items filtered by tags
     */
    public function getItemsByTags(Request $request)
    {
        $type = $request->get('type');
        $tagIds = explode(',', $request->get('tag_ids', ''));
        
        if (empty($tagIds)) {
            return response()->json([]);
        }

        // Get the full class name from the registry
        $registry = app(\Elevate\Collections\Services\CollectableRegistry::class);
        $modelClass = $registry->getByKey($type);
        
        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json([]);
        }

        // Check if model uses HasTags trait
        $usesTags = in_array(\Elevate\CommerceCore\Traits\HasTags::class, class_uses_recursive($modelClass));
        
        if (!$usesTags) {
            return response()->json([]);
        }

        // Fetch items with tags
        $items = $modelClass::query()
            ->whereHas('tags', function($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->select('id')
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), 'name'),
                fn($q) => $q->addSelect('name'),
                fn($q) => $q->addSelect('title as name')
            )
            ->get();
        
        return response()->json($items);
    }

    /**
     * Add items to collection
     */
    public function addItem(Request $request, Collection $collection)
    {
        $mode = $request->get('mode', 'manual');
        
        if ($mode === 'smart') {
            // Smart mode: Add items by tags
            $validated = $request->validate([
                'collectable_type' => 'required|string',
                'tag_ids' => 'required|string',
            ]);

            $tagIds = explode(',', $validated['tag_ids']);
            $type = $validated['collectable_type'];
            
            // Get items with these tags
            $items = match($type) {
                'Elevate\\Product\\Models\\Product' => \Elevate\Product\Models\Product::query()
                    ->whereHas('tags', function($query) use ($tagIds) {
                        $query->whereIn('tags.id', $tagIds);
                    })
                    ->pluck('id'),
                'Elevate\\Editor\\Models\\Page' => \Elevate\Editor\Models\Page::query()
                    ->whereHas('tags', function($query) use ($tagIds) {
                        $query->whereIn('tags.id', $tagIds);
                    })
                    ->pluck('id'),
                'Elevate\\Collections\\Models\\Collection' => Collection::query()
                    ->whereHas('tags', function($query) use ($tagIds) {
                        $query->whereIn('tags.id', $tagIds);
                    })
                    ->pluck('id'),
                default => collect([])
            };
            
            $ids = $items->toArray();
        } else {
            // Manual mode: Add specific items
            $validated = $request->validate([
                'collectable_type' => 'required|string',
                'collectable_ids' => 'required|string',
            ]);

            $ids = explode(',', $validated['collectable_ids']);
            $type = $validated['collectable_type'];
        }
        
        foreach ($ids as $index => $id) {
            // Check if item already exists
            $exists = $collection->collectables()
                ->where('collectable_type', $type)
                ->where('collectable_id', $id)
                ->exists();
                
            if (!$exists) {
                $collection->collectables()->create([
                    'collectable_type' => $type,
                    'collectable_id' => $id,
                    'sort_order' => $collection->collectables()->count() + $index,
                ]);
            }
        }

        return back()->with('success', count($ids) . ' item(s) added to collection.');
    }

    /**
     * Remove item from collection
     */
    public function removeItem(Collection $collection, $collectableId)
    {
        $collection->collectables()->where('id', $collectableId)->delete();

        return back()->with('success', 'Item removed from collection.');
    }

    /**
     * Bulk remove items from collection
     */
    public function bulkRemoveItems(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'collectable_ids' => 'required|string',
        ]);

        $ids = explode(',', $validated['collectable_ids']);
        
        $collection->collectables()->whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' item(s) removed from collection.'
        ]);
    }

    /**
     * Update items sort order
     */
    public function updateSort(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            $collection->collectables()
                ->where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
