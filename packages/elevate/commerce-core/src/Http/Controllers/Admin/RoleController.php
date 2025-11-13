<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): View
    {
        $query = Role::withCount('users');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('guard_name')) {
            $query->where('guard_name', $request->guard_name);
        }
        
        $roles = $query->orderBy('name')->paginate(15);
        
        // Prepare data array
        $tableData = $roles->map(function($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name ?? $role->name,
                'description' => $role->description ?? '-',
                'guard_name' => $role->guard_name,
                'users_count' => $role->users_count ?? 0,
                'created_at' => $role->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'name' => [
                'label' => 'Role Name',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['display_name'].'</div>
                            <div class="text-sm text-gray-500">'.$row['name'].'</div>
                        </div>
                    ';
                }
            ],
            'description' => [
                'label' => 'Description',
                'sortable' => false,
            ],
            'guard_name' => [
                'label' => 'Guard',
                'sortable' => true,
                'render' => function($row) {
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">'.$row['guard_name'].'</span>';
                }
            ],
            'users_count' => [
                'label' => 'Users',
                'sortable' => false,
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
                    $editUrl = route('admin.settings.roles.edit', $row['id']);
                    $buttons = '<div class="flex items-center gap-2">';
                    $buttons .= '<a href="'.$editUrl.'" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-md hover:bg-blue-200">Edit</a>';
                    
                    if ($row['users_count'] == 0) {
                        $buttons .= '<button onclick="confirmDeleteRole('.$row['id'].')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">Delete</button>';
                    }
                    
                    $buttons .= '</div>';
                    return $buttons;
                }
            ],
        ];
        
        return view('commerce::admin.settings.roles.index', [
            'roles' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $roles,
            ],
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.roles.form');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web,api,staff',
        ]);

        try {
            Role::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'] ?? null,
                'description' => $validated['description'] ?? null,
                'guard_name' => $validated['guard_name'],
            ]);

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create role: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(int $id): View
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);
            return view('commerce::admin.settings.roles.form', compact('role'));
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.roles.index')
                ->withErrors(['error' => 'Role not found']);
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web,api,staff',
        ]);

        try {
            $role = Role::findOrFail($id);
            
            $role->update([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'] ?? null,
                'description' => $validated['description'] ?? null,
                'guard_name' => $validated['guard_name'],
            ]);

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update role: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(int $id)
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);
            
            if ($role->users_count > 0) {
                return redirect()->back()->withErrors(['error' => 'Cannot delete role with assigned users']);
            }
            
            $role->delete();

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete role: ' . $e->getMessage()]);
        }
    }
}
