<?php

namespace Elevate\Watches\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\Watches\Models\Watch;
use Elevate\Editor\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WatchController extends Controller
{
    public function index()
    {
        $watches = Watch::with('template')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('watches::admin.watches.index', compact('watches'));
    }

    public function create()
    {
        $templates = Template::where('model_type', Watch::class)
            ->orWhere('is_default', true)
            ->get();

        return view('watches::admin.watches.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:watches,slug',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'movement_type' => 'nullable|string|max:255',
            'case_material' => 'nullable|string|max:255',
            'case_diameter' => 'nullable|numeric',
            'water_resistance' => 'nullable|integer',
            'strap_material' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:255|unique:watches,sku',
            'barcode' => 'nullable|string|max:255',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'template_id' => 'nullable|exists:templates,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle empty template_id
        if (empty($validated['template_id'])) {
            $validated['template_id'] = null;
        }

        $watch = Watch::create($validated);

        return redirect()
            ->route('admin.watches.edit', $watch)
            ->with('success', 'Watch created successfully.');
    }

    public function edit(Watch $watch)
    {
        $templates = Template::where('model_type', Watch::class)
            ->orWhere('is_default', true)
            ->get();

        return view('watches::admin.watches.edit', compact('watch', 'templates'));
    }

    public function update(Request $request, Watch $watch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:watches,slug,' . $watch->id,
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'movement_type' => 'nullable|string|max:255',
            'case_material' => 'nullable|string|max:255',
            'case_diameter' => 'nullable|numeric',
            'water_resistance' => 'nullable|integer',
            'strap_material' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:255|unique:watches,sku,' . $watch->id,
            'barcode' => 'nullable|string|max:255',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,active,archived',
            'template_id' => 'nullable|exists:templates,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle empty template_id
        if (empty($validated['template_id'])) {
            $validated['template_id'] = null;
        }

        $watch->update($validated);

        return redirect()
            ->route('admin.watches.edit', $watch)
            ->with('success', 'Watch updated successfully.');
    }

    public function destroy(Watch $watch)
    {
        $watch->delete();

        return redirect()
            ->route('admin.watches.index')
            ->with('success', 'Watch deleted successfully.');
    }
}
