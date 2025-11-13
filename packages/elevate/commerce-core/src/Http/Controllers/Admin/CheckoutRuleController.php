<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\CheckoutRule;
use Elevate\CommerceCore\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutRuleController extends Controller
{
    /**
     * Display a listing of checkout rules.
     */
    public function index(Request $request): View
    {
        $query = CheckoutRule::query();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
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
        
        $checkoutRules = $query->orderBy('priority', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);
        
        // Prepare data array
        $tableData = $checkoutRules->map(function($rule) {
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'type' => $rule->type,
                'description' => $rule->description,
                'is_active' => $rule->is_active,
                'priority' => $rule->priority,
                'created_at' => $rule->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'Name',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['name'].'</div>
                            <div class="text-sm text-gray-500">'.($row['description'] ? substr($row['description'], 0, 60).'...' : 'No description').'</div>
                        </div>
                    ';
                }
            ],
            'type' => [
                'label' => 'Type', 
                'sortable' => true,
                'render' => function($row) {
                    $typeLabels = [
                        'minimum_order' => 'Minimum Order',
                        'maximum_order' => 'Maximum Order',
                        'restricted_products' => 'Restricted Products',
                        'restricted_categories' => 'Restricted Categories',
                        'customer_group' => 'Customer Group',
                        'custom' => 'Custom'
                    ];
                    return $typeLabels[$row['type']] ?? $row['type'];
                }
            ],
            'priority' => [
                'label' => 'Priority',
                'sortable' => true,
            ],
            'is_active' => [
                'label' => 'Status', 
                'sortable' => true,
                'render' => function($row) {
                    return $row['is_active']
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                        : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>';
                }
            ],
            'created_at' => [
                'label' => 'Created',
                'sortable' => true,
                'render' => function($row) {
                    return '<span class="text-sm text-gray-600">'.$row['created_at']->format('M j, Y').'</span>';
                }
            ],
            'actions' => [
                'label' => 'Actions',
                'sortable' => false,
                'render' => function($row) {
                    $editUrl = route('admin.settings.checkout-rules.edit', $row['id']);
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
        
        return view('commerce::admin.settings.checkout-rules.index', [
            'checkoutRules' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $checkoutRules,
            ],
        ]);
    }

    /**
     * Show the form for creating a new checkout rule.
     */
    public function create(): View
    {
        $types = [
            'minimum_order' => ['label' => 'Minimum Order Amount'],
            'maximum_order' => ['label' => 'Maximum Order Amount'],
            'restricted_products' => ['label' => 'Restricted Products'],
            'restricted_categories' => ['label' => 'Restricted Categories'],
            'customer_group' => ['label' => 'Customer Group Restriction'],
            'custom' => ['label' => 'Custom Rule'],
        ];

        $actionTypes = [
            'block_checkout' => ['label' => 'Block Checkout'],
            'show_message' => ['label' => 'Show Message'],
            'apply_discount' => ['label' => 'Apply Discount'],
            'require_approval' => ['label' => 'Require Approval'],
            'redirect' => ['label' => 'Redirect'],
        ];

        $customerGroups = CustomerGroup::orderBy('name')->get();

        return view('commerce::admin.settings.checkout-rules.form', compact('types', 'actionTypes', 'customerGroups'));
    }

    /**
     * Store a newly created checkout rule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:50',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;
        $validated['priority'] = $validated['priority'] ?? 0;

        CheckoutRule::create($validated);

        return redirect()->route('admin.settings.checkout-rules.index')
            ->with('success', 'Checkout rule created successfully');
    }

    /**
     * Show the form for editing the specified checkout rule.
     */
    public function edit(int $id): View
    {
        $checkoutRule = CheckoutRule::findOrFail($id);
        
        // Convert JSON fields to arrays if they're strings
        if (is_string($checkoutRule->conditions)) {
            $checkoutRule->conditions = json_decode($checkoutRule->conditions, true) ?? [];
        }
        if (is_string($checkoutRule->actions)) {
            $checkoutRule->actions = json_decode($checkoutRule->actions, true) ?? [];
        }
        
        // Extract action_type from actions array if it exists and ensure it's a string
        if (is_array($checkoutRule->actions) && isset($checkoutRule->actions['type'])) {
            $checkoutRule->action_type = (string) $checkoutRule->actions['type'];
        } elseif (isset($checkoutRule->action_type) && is_array($checkoutRule->action_type)) {
            // If action_type itself is an array, convert to empty string
            $checkoutRule->action_type = '';
        } elseif (!isset($checkoutRule->action_type)) {
            $checkoutRule->action_type = '';
        } else {
            // Ensure it's a string
            $checkoutRule->action_type = (string) $checkoutRule->action_type;
        }
        
        $types = [
            'minimum_order' => ['label' => 'Minimum Order Amount'],
            'maximum_order' => ['label' => 'Maximum Order Amount'],
            'restricted_products' => ['label' => 'Restricted Products'],
            'restricted_categories' => ['label' => 'Restricted Categories'],
            'customer_group' => ['label' => 'Customer Group Restriction'],
            'custom' => ['label' => 'Custom Rule'],
        ];

        $actionTypes = [
            'block_checkout' => ['label' => 'Block Checkout'],
            'show_message' => ['label' => 'Show Message'],
            'apply_discount' => ['label' => 'Apply Discount'],
            'require_approval' => ['label' => 'Require Approval'],
            'redirect' => ['label' => 'Redirect'],
        ];

        $customerGroups = CustomerGroup::orderBy('name')->get();
        
        return view('commerce::admin.settings.checkout-rules.form', compact('checkoutRule', 'types', 'actionTypes', 'customerGroups'));
    }

    /**
     * Update the specified checkout rule.
     */
    public function update(Request $request, int $id)
    {
        $checkoutRule = CheckoutRule::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:50',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;
        $validated['priority'] = $validated['priority'] ?? 0;

        $checkoutRule->update($validated);

        return redirect()->route('admin.settings.checkout-rules.index')
            ->with('success', 'Checkout rule updated successfully');
    }

    /**
     * Remove the specified checkout rule.
     */
    public function destroy(int $id)
    {
        $checkoutRule = CheckoutRule::findOrFail($id);
        $checkoutRule->delete();

        return redirect()->route('admin.settings.checkout-rules.index')
            ->with('success', 'Checkout rule deleted successfully');
    }
}
