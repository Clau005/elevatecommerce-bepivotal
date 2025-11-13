<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    /**
     * Display a listing of tags
     */
    public function index(Request $request)
    {
        $query = Tag::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('value', 'like', "%{$search}%")
                  ->orWhere('handle', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'value');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validate sort fields
        $allowedSorts = ['value', 'handle', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'value';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Get per_page from request, default to 25
        $perPage = $request->get('per_page', 25);
        
        // Server-side pagination with sorting
        $tags = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $tags->appends($request->query());
        
        // Prepare data for table
        $tableData = $tags->map(function($tag) {
            return [
                'id' => $tag->id,
                'value' => $tag->value,
                'handle' => $tag->handle,
                'created_at' => $tag->created_at,
                'usage_count' => $tag->taggables()->count(),
            ];
        })->toArray();

        // Define columns with custom rendering
        $columns = [
            'value' => [
                'label' => 'Tag',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm font-medium text-gray-900">'.$row['value'].'</div>';
                }
            ],
            'handle' => [
                'label' => 'Handle',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm text-gray-500 font-mono">'.$row['handle'].'</div>';
                }
            ],
            'usage_count' => [
                'label' => 'Used',
                'render' => function($row) {
                    return '<div class="text-sm text-gray-600">'.$row['usage_count'].' items</div>';
                }
            ],
            'created_at' => [
                'label' => 'Created',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm text-gray-500">'.$row['created_at']->format('M d, Y').'</div>';
                }
            ],
            'actions' => [
                'label' => '',
                'render' => function($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <a href="'.route('admin.tags.edit', $row['id']).'" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                Edit
                            </a>
                            <button onclick="confirmDelete('.$row['id'].')" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                Delete
                            </button>
                        </div>
                    ';
                }
            ],
        ];

        return view('commerce::admin.tags.index', [
            'data' => $tableData,
            'columns' => $columns,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        return view('commerce::admin.tags.create');
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:tags,value',
            'handle' => 'nullable|string|max:255|unique:tags,handle',
        ]);

        try {
            $tag = Tag::create($validated);

            Log::info('Tag created', [
                'tag_id' => $tag->id,
                'value' => $tag->value,
                'created_by' => auth('staff')->user()?->id,
            ]);

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create tag: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create tag.');
        }
    }

    /**
     * Show the form for editing a tag
     */
    public function edit(Tag $tag)
    {
        return view('commerce::admin.tags.edit', [
            'tag' => $tag,
            'usageCount' => $tag->taggables()->count(),
        ]);
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:tags,value,' . $tag->id,
            'handle' => 'nullable|string|max:255|unique:tags,handle,' . $tag->id,
        ]);

        try {
            $tag->update($validated);

            Log::info('Tag updated', [
                'tag_id' => $tag->id,
                'value' => $tag->value,
                'updated_by' => auth('staff')->user()?->id,
            ]);

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update tag: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update tag.');
        }
    }

    /**
     * Remove the specified tag
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        try {
            $usageCount = $tag->taggables()->count();
            
            Log::info('Tag deleted', [
                'tag_id' => $tag->id,
                'value' => $tag->value,
                'usage_count' => $usageCount,
                'deleted_by' => auth('staff')->user()?->id,
            ]);

            $tag->delete();

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tag deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete tag: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete tag.');
        }
    }

    /**
     * Merge tags - combine multiple tags into one
     */
    public function merge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_tag_ids' => 'required|array|min:1',
            'source_tag_ids.*' => 'exists:tags,id',
            'target_tag_id' => 'required|exists:tags,id',
        ]);

        try {
            $targetTag = Tag::findOrFail($validated['target_tag_id']);
            $sourceTags = Tag::whereIn('id', $validated['source_tag_ids'])->get();

            foreach ($sourceTags as $sourceTag) {
                if ($sourceTag->id === $targetTag->id) {
                    continue;
                }

                // Move all taggables to target tag
                $sourceTag->taggables()->each(function($taggable) use ($targetTag) {
                    // Check if target tag is already attached to this model
                    $exists = $targetTag->taggables()
                        ->where('taggable_id', $taggable->taggable_id)
                        ->where('taggable_type', $taggable->taggable_type)
                        ->exists();

                    if (!$exists) {
                        $taggable->update(['tag_id' => $targetTag->id]);
                    } else {
                        $taggable->delete();
                    }
                });

                $sourceTag->delete();
            }

            Log::info('Tags merged', [
                'source_tag_ids' => $validated['source_tag_ids'],
                'target_tag_id' => $validated['target_tag_id'],
                'merged_by' => auth('staff')->user()?->id,
            ]);

            return redirect()->route('admin.tags.index')
                ->with('success', 'Tags merged successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to merge tags: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to merge tags.');
        }
    }

    /**
     * Get all tags for AJAX requests
     */
    public function getAllTags()
    {
        try {
            $tags = Tag::orderBy('value')->get(['id', 'value', 'handle']);
            return response()->json($tags);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tags: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch tags'], 500);
        }
    }
}
