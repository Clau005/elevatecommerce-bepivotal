<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerGroupController extends Controller
{
    /**
     * Display a listing of customer groups.
     */
    public function index(Request $request): View
    {
        $query = CustomerGroup::withCount('customers as customers_count');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('handle', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('is_default')) {
            $query->where('is_default', true);
        }
        
        $customerGroups = $query->orderBy('is_default', 'desc')
                                ->orderBy('name')
                                ->paginate(15);
        
        // Prepare data array
        $tableData = $customerGroups->map(function($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'handle' => $group->handle,
                'is_default' => $group->is_default,
                'customers_count' => $group->customers_count,
                'created_at' => $group->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'Group Name',
                'sortable' => true,
            ],
            'handle' => [
                'label' => 'Handle',
                'sortable' => true,
            ],
            'customers_count' => [
                'label' => 'Customers',
                'sortable' => false,
            ],
            'is_default' => [
                'label' => 'Default',
                'sortable' => true,
                'render' => function($row) {
                    return $row['is_default'] 
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Default</span>'
                        : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">-</span>';
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
                    $editUrl = route('admin.settings.customer-groups.edit', $row['id']);
                    $buttons = '<div class="flex items-center gap-2">';
                    $buttons .= '<a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">Edit</a>';
                    
                    if (!$row['is_default'] && $row['customers_count'] == 0) {
                        $buttons .= '<button onclick="confirmDeleteGroup('.$row['id'].')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Delete</button>';
                    }
                    
                    $buttons .= '</div>';
                    return $buttons;
                }
            ],
        ];
        
        return view('commerce::admin.settings.customer-groups.index', [
            'customerGroups' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $customerGroups,
            ],
        ]);
    }

    /**
     * Show the form for creating a new customer group.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.customer-groups.form');
    }

    /**
     * Store a newly created customer group.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:customer_groups,handle',
            'is_default' => 'boolean',
        ]);

        // Auto-generate handle if not provided
        if (empty($validated['handle'])) {
            $validated['handle'] = Str::slug($validated['name']);
        }

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            CustomerGroup::where('is_default', true)->update(['is_default' => false]);
        }

        CustomerGroup::create($validated);

        return redirect()->route('admin.settings.customer-groups.index')
            ->with('success', 'Customer group created successfully');
    }

    /**
     * Show the form for editing the specified customer group.
     */
    public function edit(int $id): View
    {
        $customerGroup = CustomerGroup::withCount('customers')->findOrFail($id);
        return view('commerce::admin.settings.customer-groups.form', compact('customerGroup'));
    }

    /**
     * Update the specified customer group.
     */
    public function update(Request $request, int $id)
    {
        $customerGroup = CustomerGroup::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'required|string|max:255|unique:customer_groups,handle,' . $id,
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            CustomerGroup::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $customerGroup->update($validated);

        return redirect()->route('admin.settings.customer-groups.index')
            ->with('success', 'Customer group updated successfully');
    }

    /**
     * Remove the specified customer group.
     */
    public function destroy(int $id)
    {
        $customerGroup = CustomerGroup::withCount('customers')->findOrFail($id);
        
        if ($customerGroup->is_default) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete default customer group']);
        }
        
        if ($customerGroup->customers_count > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete customer group with assigned customers']);
        }
        
        $customerGroup->delete();

        return redirect()->route('admin.settings.customer-groups.index')
            ->with('success', 'Customer group deleted successfully');
    }
}
