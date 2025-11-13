<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\User;
use Elevate\CommerceCore\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CustomerWebController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
    
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('account_reference', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
    
     
    
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
    
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    
        // Sorting functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Validate sort column
        $allowedSortColumns = ['first_name', 'last_name', 'email', 'account_reference', 'company_name', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
    
        // Get per_page from request, default to 4 for testing
        $perPage = $request->get('per_page', 20);
        
        // Server-side pagination with sorting
        $customers = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $customers->appends($request->query());
        
        // Transform data to arrays
        $tableData = $customers->map(function($customer) {
            return [
                'id' => $customer->id,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'account_reference' => $customer->account_reference ?? '-',
                'company_name' => $customer->company_name ?? '-',
                'orders_count' => $customer->orders()->count(),
                'created_at' => $customer->created_at,
            ];
        })->toArray();
        
        // Define columns with render functions
        $columns = [
            'full_name' => [
                'label' => 'Customer',
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['full_name'].'</div>
                            <div class="text-sm text-gray-500">'.$row['account_reference'].'</div>
                        </div>
                    ';
                }
            ],
            'email' => [
                'label' => 'Email',
                'class' => 'text-sm text-gray-900',
            ],
            'company_name' => [
                'label' => 'Company',
                'class' => 'text-sm text-gray-500',
            ],
            'orders_count' => [
                'label' => 'Orders',
                'class' => 'text-sm text-gray-500',
            ],
            'created_at' => [
                'label' => 'Joined',
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm text-gray-900">'.$row['created_at']->format('M d, Y').'</div>
                            <div class="text-xs text-gray-500">'.$row['created_at']->diffForHumans().'</div>
                        </div>
                    ';
                }
            ],
            'actions' => [
                'label' => '',
                'class' => 'text-right text-sm font-medium',
                'render' => function($row) {
                    return '
                        <a href="'.route('admin.customers.show', $row['id']).'" class="text-blue-600 hover:text-blue-900">
                            View
                        </a>
                    ';
                }
            ],
        ];

        return view('commerce::admin.customers.index', [
            'data' => $tableData,
            'columns' => $columns,
            'customers' => $customers,
        ]);
    }

    public function show(User $customer): View
    {
        // Load customer's orders with pagination
        $orders = $customer->orders()
            ->with(['channel'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Customer statistics
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('status', 'completed')->sum('total') / 100, // Convert from cents
            'average_order_value' => $customer->orders()->where('status', 'completed')->avg('total') / 100,
            'last_order_date' => $customer->orders()->latest()->first()?->created_at,
            'account_age_days' => $customer->created_at->diffInDays(now()),
        ];

        return view('commerce::admin.customers.show', compact('customer', 'orders', 'stats'));
    }

    public function create()
    {
        return view('commerce::admin.customers.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'company_name' => 'nullable|string|max:255',
            'tax_identifier' => 'nullable|string|max:255',
            'account_reference' => 'nullable|string|max:255|unique:users,account_reference',
            'customer_group_id' => 'nullable|integer',
            'send_welcome_email' => 'nullable|boolean',
        ]);

        // Generate account reference if not provided
        if (empty($validated['account_reference'])) {
            $validated['account_reference'] = 'CUST' . str_pad(User::count() + 1, 6, '0', STR_PAD_LEFT);
        }
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        $customer = User::create($validated);

        // TODO: Send welcome email if requested
        if ($request->boolean('send_welcome_email')) {
            // Mail::to($customer->email)->send(new WelcomeEmail($customer));
        }

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function edit(User $customer): View
    {
        return view('commerce::admin.customers.form', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:10',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'password' => 'nullable|string|min:8',
            'company_name' => 'nullable|string|max:255',
            'tax_identifier' => 'nullable|string|max:255',
            'customer_group_id' => 'nullable|integer',
            'send_notification_email' => 'nullable|boolean',
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        // TODO: Send notification email if requested
        if ($request->boolean('send_notification_email')) {
            // Mail::to($customer->email)->send(new AccountUpdatedEmail($customer));
        }

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer): RedirectResponse
    {
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return redirect()->route('admin.customers.show', $customer)
                ->with('error', 'Cannot delete customer with existing orders.');
        }

        $customerName = $customer->full_name;
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$customerName}' deleted successfully.");
    }
}
