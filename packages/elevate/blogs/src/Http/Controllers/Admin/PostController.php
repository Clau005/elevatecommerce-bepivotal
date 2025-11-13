<?php

namespace Elevate\Blogs\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\Blogs\Models\Post;
use Elevate\CommerceCore\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index(Request $request)
    {
        $query = Post::with('author', 'tags');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Author filter
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->get('author_id'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate(20);

        // Prepare data for x-table component
        $data = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'author' => $post->author?->full_name ?? 'N/A',
                'status' => $post->status,
                'published_at' => $post->published_at?->format('M d, Y') ?? 'Not published',
                'created_at' => $post->created_at->format('M d, Y'),
            ];
        });

        $columns = [
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'author', 'label' => 'Author', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'published_at', 'label' => 'Published', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
        ];

        $authors = \Elevate\CommerceCore\Models\Staff::orderBy('first_name')->get();

        return view('blogs::admin.posts.index', compact('data', 'columns', 'posts', 'authors'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $allTags = Tag::orderBy('value')->get();
        
        return view('blogs::admin.posts.create', compact('allTags'));
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Post::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set author
        $validated['author_id'] = auth('staff')->id();

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        // Sync tags
        if ($request->has('tags')) {
            $tagIds = collect(json_decode($request->input('tags'), true))->pluck('id')->toArray();
            $post->syncTags($tagIds);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        $allTags = Tag::orderBy('value')->get();
        $postTags = $post->tags;
        
        return view('blogs::admin.posts.edit', compact('post', 'allTags', 'postTags'));
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Post::where('slug', $validated['slug'])->where('id', '!=', $post->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at']) && $post->status !== 'published') {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Sync tags
        if ($request->has('tags')) {
            $tagIds = collect(json_decode($request->input('tags'), true))->pluck('id')->toArray();
            $post->syncTags($tagIds);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            
            return redirect()->route('admin.posts.index')
                ->with('success', 'Post deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete post: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete post.');
        }
    }
}
