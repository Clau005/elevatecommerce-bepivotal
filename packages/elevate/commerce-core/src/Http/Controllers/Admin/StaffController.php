<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request): View
    {
        $query = Staff::with(['roles']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('is_super_admin')) {
            $query->where('is_super_admin', true);
        }
        
        $staff = $query->paginate(15);
        
        // Prepare data array
        $tableData = $staff->map(function($member) {
            return [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'full_name' => trim($member->first_name . ' ' . $member->last_name),
                'email' => $member->email,
                'phone' => $member->phone ?? 'N/A',
                'is_super_admin' => $member->is_super_admin,
                'roles' => $member->roles->pluck('name')->implode(', ') ?: 'No roles',
                'created_at' => $member->created_at,
            ];
        })->toArray();
        
        // Define columns
        $columns = [
            'full_name' => [
                'label' => 'Name',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['full_name'].'</div>
                            <div class="text-sm text-gray-500">'.$row['email'].'</div>
                        </div>
                    ';
                }
            ],
            'phone' => [
                'label' => 'Phone',
                'sortable' => false,
            ],
            'roles' => [
                'label' => 'Roles',
                'sortable' => false,
                'render' => function($row) {
                    return '<span class="text-sm text-gray-600">'.$row['roles'].'</span>';
                }
            ],
            'is_super_admin' => [
                'label' => 'Super Admin',
                'sortable' => true,
                'render' => function($row) {
                    return $row['is_super_admin'] 
                        ? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>'
                        : '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No</span>';
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
                    $editUrl = route('admin.settings.staff.edit', $row['id']);
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
        
        return view('commerce::admin.settings.staff.index', [
            'staff' => [
                'data' => $tableData,
                'columns' => $columns,
                'paginator' => $staff,
            ],
        ]);
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create(): View
    {
        return view('commerce::admin.settings.staff.form');
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'is_super_admin' => 'boolean',
        ]);

        Staff::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'is_super_admin' => $validated['is_super_admin'] ?? false,
        ]);

        return redirect()->route('admin.settings.staff.index')
            ->with('success', 'Staff member created successfully');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(int $id): View
    {
        $staff = Staff::findOrFail($id);
        return view('commerce::admin.settings.staff.form', compact('staff'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, int $id)
    {
        $staff = Staff::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'is_super_admin' => 'boolean',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_super_admin' => $validated['is_super_admin'] ?? false,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $staff->update($updateData);

        return redirect()->route('admin.settings.staff.index')
            ->with('success', 'Staff member updated successfully');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(int $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.settings.staff.index')
            ->with('success', 'Staff member deleted successfully');
    }
}
