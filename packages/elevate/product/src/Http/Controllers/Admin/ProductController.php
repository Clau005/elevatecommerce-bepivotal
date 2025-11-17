<?php

namespace Elevate\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\Product\Models\Product;
use Elevate\Product\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with('variants');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Prepare table data
        $tableData = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'featured_image' => $product->featured_image,
                'type' => $product->type,
                'variant_count' => $product->variants->count(),
                'status' => $product->status,
                'price' => $product->price,
                'track_inventory' => $product->track_inventory,
                'stock' => $product->getStockLevel(),
            ];
        })->toArray();

        // Define columns
        $columns = [
            'name' => [
                'label' => 'Product',
                'render' => function($row) {
                    $image = $row['featured_image'] 
                        ? '<img src="'.$row['featured_image'].'" alt="'.$row['name'].'" class="h-10 w-10 rounded object-cover">'
                        : '<div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                          </div>';
                    
                    $sku = $row['sku'] ? '<div class="text-sm text-gray-500">SKU: '.$row['sku'].'</div>' : '';
                    
                    return '
                        <div class="flex items-center">
                            '.$image.'
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">'.$row['name'].'</div>
                                '.$sku.'
                            </div>
                        </div>
                    ';
                }
            ],
            'type' => [
                'label' => 'Type',
                'render' => function($row) {
                    $typeClass = $row['type'] === 'simple' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
                    $typeText = ucfirst($row['type']);
                    if ($row['type'] === 'variable' && $row['variant_count'] > 0) {
                        $typeText .= ' ('.$row['variant_count'].')';
                    }
                    return '<span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 '.$typeClass.'">'.$typeText.'</span>';
                }
            ],
            'status' => [
                'label' => 'Status',
                'render' => function($row) {
                    $statusClasses = [
                        'active' => 'bg-green-100 text-green-800',
                        'draft' => 'bg-yellow-100 text-yellow-800',
                        'archived' => 'bg-gray-100 text-gray-800',
                    ];
                    $statusClass = $statusClasses[$row['status']] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 '.$statusClass.'">'.ucfirst($row['status']).'</span>';
                }
            ],
            'price' => [
                'label' => 'Price',
                'render' => function($row) {
                    return '<span class="text-sm text-gray-900">£'.number_format($row['price'], 2).'</span>';
                }
            ],
            'stock' => [
                'label' => 'Stock',
                'render' => function($row) {
                    return $row['track_inventory']
                        ? '<span class="text-sm text-gray-900">'.($row['stock'] ?? 0).'</span>'
                        : '<span class="text-sm text-gray-400">Not tracked</span>';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'class' => 'text-right text-sm font-medium whitespace-nowrap',
                'render' => function($row) {
                    $html = '<div class="flex items-center justify-end gap-2">';
                    
                    $html .= '<a href="'.route('admin.products.edit', $row['id']).'" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                        Edit
                    </a>';
                    
                    if ($row['type'] === 'variable') {
                        $html .= '<a href="'.route('admin.products.variants', $row['id']).'" class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700 transition-colors">
                            Variants
                        </a>';
                    }
                    
                    $html .= '<form action="'.route('admin.products.destroy', $row['id']).'" method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this product?\')">
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

        return view('product::admin.products.index', compact('products', 'tableData', 'columns'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $templates = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Product\Models\Product')
            ->get();

        $allTags = \Elevate\CommerceCore\Models\Tag::ordered()->get();

        return view('product::admin.products.create', compact('templates', 'allTags'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'type' => 'required|in:simple,variable',
            'status' => 'required|in:draft,active,archived',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|in:kg,g,lb,oz',
            'requires_shipping' => 'boolean',
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:1',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'template_id' => 'nullable|exists:templates,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set defaults
        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['requires_shipping'] = $request->has('requires_shipping');
        $validated['is_taxable'] = $request->has('is_taxable');

        $product = Product::create($validated);

        // Handle tags
        if ($request->filled('tags')) {
            $product->syncTags($request->tags);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('variants', 'template');

        return view('product::admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $templates = \Elevate\Editor\Models\Template::where('model_type', 'Elevate\Product\Models\Product')
            ->get();

        $product->load('variants', 'tags');
        
        $allTags = \Elevate\CommerceCore\Models\Tag::ordered()->get();

        return view('product::admin.products.edit', compact('product', 'templates', 'allTags'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'type' => 'required|in:simple,variable',
            'status' => 'required|in:draft,active,archived',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|in:kg,g,lb,oz',
            'requires_shipping' => 'boolean',
            'is_taxable' => 'boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:1',
            'featured_image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'template_id' => 'nullable|exists:templates,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Set defaults
        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['requires_shipping'] = $request->has('requires_shipping');
        $validated['is_taxable'] = $request->has('is_taxable');

        $product->update($validated);

        // Handle tags
        if ($request->has('tags')) {
            $product->syncTags($request->tags ?? []);
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Display variants for a product
     */
    public function variants(Product $product)
    {
        $product->load('variants');

        return view('product::admin.products.variants', compact('product'));
    }

    /**
     * Store a new variant
     */
    public function storeVariant(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|string',
            'option1_name' => 'nullable|string|max:255',
            'option1_value' => 'nullable|string|max:255',
            'option2_name' => 'nullable|string|max:255',
            'option2_value' => 'nullable|string|max:255',
            'option3_name' => 'nullable|string|max:255',
            'option3_value' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['product_id'] = $product->id;
        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['is_active'] = $request->has('is_active');

        $variant = ProductVariant::create($validated);

        return redirect()
            ->route('admin.products.variants', $product)
            ->with('success', 'Variant created successfully!');
    }

    /**
     * Update a variant
     */
    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'track_inventory' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'image' => 'nullable|string',
            'option1_name' => 'nullable|string|max:255',
            'option1_value' => 'nullable|string|max:255',
            'option2_name' => 'nullable|string|max:255',
            'option2_value' => 'nullable|string|max:255',
            'option3_name' => 'nullable|string|max:255',
            'option3_value' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['is_active'] = $request->has('is_active');

        $variant->update($validated);

        return redirect()
            ->route('admin.products.variants', $product)
            ->with('success', 'Variant updated successfully!');
    }

    /**
     * Delete a variant
     */
    public function destroyVariant(Product $product, ProductVariant $variant)
    {
        $variant->delete();

        return redirect()
            ->route('admin.products.variants', $product)
            ->with('success', 'Variant deleted successfully!');
    }
}
