<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscountController extends Controller
{
    /**
     * Display a listing of discounts.
     */
    public function index(Request $request): View
    {
        $query = Discount::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('handle', 'like', "%{$search}%")
                  ->orWhere('coupon_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $discounts = $query->orderBy('priority', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);
        
        // Prepare data array
        $tableData = $discounts->map(function($discount) {
            return [
                'id' => $discount->id,
                'name' => $discount->name,
                'type' => $discount->type,
                'value' => $discount->value,
                'coupon_code' => $discount->coupon_code,
                'is_automatic' => $discount->is_automatic,
                'is_active' => $discount->is_active,
                'usage_count' => $discount->usage_count ?? 0,
                'usage_limit' => $discount->usage_limit,
                'starts_at' => $discount->starts_at,
                'expires_at' => $discount->expires_at,
                'created_at' => $discount->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'Name',
                'sortable' => true,
            ],
            'type' => [
                'label' => 'Type', 
                'sortable' => true,
                'render' => function($row) {
                    $typeLabels = [
                        'percentage' => 'Percentage',
                        'fixed_amount' => 'Fixed Amount',
                        'free_shipping' => 'Free Shipping',
                        'buy_x_get_y' => 'Buy X Get Y'
                    ];
                    return $typeLabels[$row['type']] ?? $row['type'];
                }
            ],
            'value' => [
                'label' => 'Value',
                'sortable' => true,
                'render' => function($row) {
                    if ($row['type'] === 'percentage') {
                        return $row['value'] ? $row['value'] . '%' : 'N/A';
                    } elseif ($row['type'] === 'fixed_amount') {
                        return $row['value'] ? '£' . number_format($row['value'] / 100, 2) : 'N/A';
                    } else {
                        return 'N/A';
                    }
                }
            ],
            'coupon_code' => [
                'label' => 'Coupon Code',
                'sortable' => true,
                'render' => function($row) {
                    if ($row['is_automatic']) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Automatic</span>';
                    }
                    return $row['coupon_code'] ? '<code class="px-2 py-1 text-xs bg-gray-100 rounded">' . $row['coupon_code'] . '</code>' : 'N/A';
                }
            ],
            'usage_count' => [
                'label' => 'Usage',
                'sortable' => false,
                'render' => function($row) {
                    $used = $row['usage_count'] ?? 0;
                    $limit = $row['usage_limit'] ?? '∞';
                    return $used . ' / ' . $limit;
                }
            ],
            'is_active' => [
                'label' => 'Status', 
                'sortable' => true,
                'render' => function($row) {
                    if (!$row['is_active']) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                    }
                    
                    $now = now();
                    if ($row['starts_at'] && $now->lt($row['starts_at'])) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Scheduled</span>';
                    }
                    if ($row['expires_at'] && $now->gt($row['expires_at'])) {
                        return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Expired</span>';
                    }
                    
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>';
                }
            ],
            'expires_at' => [
                'label' => 'Expires',
                'sortable' => true,
                'render' => function($row) {
                    return $row['expires_at'] ? $row['expires_at']->format('M j, Y') : 'Never';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $editUrl = route('admin.settings.discounts.edit', $row['id']);
                    return '
                        <div class="flex items-center gap-2">
                            <a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">
                                Edit
                            </a>
                            <button onclick="confirmDelete('.$row['id'].')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                                Delete
                            </button>
                        </div>
                    ';
                }
            ],
        ];
        
        return view('commerce::admin.settings.discounts.index', [
            'discounts' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $discounts,
            ],
        ]);
    }

    /**
     * Show the form for creating a new discount.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.discounts.form');
    }

    /**
     * Store a newly created discount.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:discounts,handle',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,free_shipping,buy_x_get_y',
            'value' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50|unique:discounts,coupon_code',
            'is_active' => 'boolean',
            'is_automatic' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'priority' => 'nullable|integer|min:0',
            'combine_with_other_discounts' => 'boolean',
        ]);

        // Convert boolean fields
        $validated['is_active'] = $validated['is_active'] ?? false;
        $validated['is_automatic'] = $validated['is_automatic'] ?? false;
        $validated['combine_with_other_discounts'] = $validated['combine_with_other_discounts'] ?? false;

        // Clear coupon code if automatic
        if ($validated['is_automatic']) {
            $validated['coupon_code'] = null;
        }

        // Clear value for free shipping and buy_x_get_y
        if (in_array($validated['type'], ['free_shipping', 'buy_x_get_y'])) {
            $validated['value'] = null;
        }

        // Convert monetary amounts to cents
        if (isset($validated['value']) && $validated['value'] !== null && $validated['type'] === 'fixed_amount') {
            $validated['value'] = (int) round($validated['value'] * 100);
        }
        if (isset($validated['minimum_order_amount']) && $validated['minimum_order_amount'] !== null) {
            $validated['minimum_order_amount'] = (int) round($validated['minimum_order_amount'] * 100);
        }
        if (isset($validated['maximum_discount_amount']) && $validated['maximum_discount_amount'] !== null) {
            $validated['maximum_discount_amount'] = (int) round($validated['maximum_discount_amount'] * 100);
        }

        Discount::create($validated);

        return redirect()->route('admin.settings.discounts.index')
            ->with('success', 'Discount created successfully');
    }

    /**
     * Show the form for editing the specified discount.
     */
    public function edit(int $id): View
    {
        $discount = Discount::findOrFail($id);
        
        // Convert cents to dollars for display
        if ($discount->type === 'fixed_amount' && $discount->value) {
            $discount->value = $discount->value / 100;
        }
        if ($discount->minimum_order_amount) {
            $discount->minimum_order_amount = $discount->minimum_order_amount / 100;
        }
        if ($discount->maximum_discount_amount) {
            $discount->maximum_discount_amount = $discount->maximum_discount_amount / 100;
        }
        
        return view('commerce::admin.settings.discounts.form', compact('discount'));
    }

    /**
     * Update the specified discount.
     */
    public function update(Request $request, int $id)
    {
        $discount = Discount::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:discounts,handle,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,free_shipping,buy_x_get_y',
            'value' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50|unique:discounts,coupon_code,' . $id,
            'is_active' => 'boolean',
            'is_automatic' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'priority' => 'nullable|integer|min:0',
            'combine_with_other_discounts' => 'boolean',
        ]);

        // Convert boolean fields
        $validated['is_active'] = $validated['is_active'] ?? false;
        $validated['is_automatic'] = $validated['is_automatic'] ?? false;
        $validated['combine_with_other_discounts'] = $validated['combine_with_other_discounts'] ?? false;

        // Clear coupon code if automatic
        if ($validated['is_automatic']) {
            $validated['coupon_code'] = null;
        }

        // Clear value for free shipping and buy_x_get_y
        if (in_array($validated['type'], ['free_shipping', 'buy_x_get_y'])) {
            $validated['value'] = null;
        }

        // Convert monetary amounts to cents
        if (isset($validated['value']) && $validated['value'] !== null && $validated['type'] === 'fixed_amount') {
            $validated['value'] = (int) round($validated['value'] * 100);
        }
        if (isset($validated['minimum_order_amount']) && $validated['minimum_order_amount'] !== null) {
            $validated['minimum_order_amount'] = (int) round($validated['minimum_order_amount'] * 100);
        }
        if (isset($validated['maximum_discount_amount']) && $validated['maximum_discount_amount'] !== null) {
            $validated['maximum_discount_amount'] = (int) round($validated['maximum_discount_amount'] * 100);
        }

        $discount->update($validated);

        return redirect()->route('admin.settings.discounts.index')
            ->with('success', 'Discount updated successfully');
    }

    /**
     * Remove the specified discount.
     */
    public function destroy(int $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('admin.settings.discounts.index')
            ->with('success', 'Discount deleted successfully');
    }
}
