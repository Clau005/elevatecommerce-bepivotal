<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class EnquiryWebController extends Controller
{
    /**
     * Display a listing of enquiries
     */
    public function index(Request $request)
    {
        $query = Enquiry::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Name filter
        if ($request->has('name_filter') && $request->name_filter) {
            $query->where('name', 'like', "%{$request->name_filter}%");
        }

        // Company filter
        if ($request->has('company_filter') && $request->company_filter) {
            $query->where('company_name', 'like', "%{$request->company_filter}%");
        }

        // Email filter
        if ($request->has('email_filter') && $request->email_filter) {
            $query->where('email', 'like', "%{$request->email_filter}%");
        }

        // Phone filter
        if ($request->has('phone_filter') && $request->phone_filter) {
            $query->where('phone', 'like', "%{$request->phone_filter}%");
        }

        // Message content filter
        if ($request->has('message_filter') && $request->message_filter) {
            $query->where('message', 'like', "%{$request->message_filter}%");
        }

        // Date range filters
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Has phone filter
        if ($request->has('has_phone') && $request->has_phone) {
            $query->whereNotNull('phone')->where('phone', '!=', '');
        }

        // Has company filter
        if ($request->has('has_company') && $request->has_company) {
            $query->whereNotNull('company_name')->where('company_name', '!=', '');
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Validate sort column
        $allowedSortColumns = ['name', 'company_name', 'email', 'phone', 'status', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Get per_page from request, default to 25
        $perPage = $request->get('per_page', 25);
        
        // Server-side pagination with sorting
        $enquiries = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $enquiries->appends($request->query());
        
        // Prepare data for table
        $tableData = $enquiries->map(function($enquiry) {
            return [
                'id' => $enquiry->id,
                'name' => $enquiry->name,
                'company_name' => $enquiry->company_name ?? '-',
                'email' => $enquiry->email,
                'phone' => $enquiry->phone ?? '-',
                'message' => $enquiry->message,
                'status' => $enquiry->status,
                'created_at' => $enquiry->created_at,
            ];
        })->toArray();

        // Define columns with custom rendering
        $columns = [
            'name' => [
                'label' => 'Name',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm font-medium text-gray-900">'.$row['name'].'</div>';
                }
            ],
            'company_name' => [
                'label' => 'Company',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm text-gray-500">'.$row['company_name'].'</div>';
                }
            ],
            'email' => [
                'label' => 'Email',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm text-gray-900">'.$row['email'].'</div>';
                }
            ],
            'phone' => [
                'label' => 'Phone',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm text-gray-500">'.$row['phone'].'</div>';
                }
            ],
            'message' => [
                'label' => 'Message',
                'render' => function($row) {
                    $truncated = strlen($row['message']) > 100 ? substr($row['message'], 0, 100) . '...' : $row['message'];
                    return '<div class="text-sm text-gray-600 max-w-md">'.$truncated.'</div>';
                }
            ],
            'status' => [
                'label' => 'Status',
                'sortable' => true,
                'render' => function($row) {
                    $colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'contacted' => 'bg-blue-100 text-blue-800',
                        'converted' => 'bg-green-100 text-green-800'
                    ];
                    $color = $colors[$row['status']] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.$color.'">'.ucfirst($row['status']).'</span>';
                }
            ],
            'created_at' => [
                'label' => 'Date',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm text-gray-900">'.$row['created_at']->format('M d, Y').'</div>
                            <div class="text-xs text-gray-500">'.$row['created_at']->format('H:i').'</div>
                        </div>
                    ';
                }
            ],
            'actions' => [
                'label' => '',
                'render' => function($row) {
                    $buttons = [];
                    
                    // Show Mark Contacted button only if status is pending
                    if ($row['status'] === 'pending') {
                        $buttons[] = '<button onclick="updateStatus('.$row['id'].', \'contacted\')" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200">Mark Contacted</button>';
                    }
                    
                    // Show Mark Converted button if status is contacted
                    if ($row['status'] === 'contacted') {
                        $buttons[] = '<button onclick="updateStatus('.$row['id'].', \'converted\')" class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200">Mark Converted</button>';
                    }
                    
                    // Always show delete button
                    $buttons[] = '<button onclick="confirmDelete('.$row['id'].')" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200" title="Delete"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></button>';
                    
                    return '<div class="flex items-center gap-2">'.implode('', $buttons).'</div>';
                }
            ],
        ];

        return view('commerce::admin.enquiries.index', [
            'data' => $tableData,
            'columns' => $columns,
            'enquiries' => $enquiries,
        ]);
    }

    /**
     * Get status badge configuration for display
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'new' => ['color' => 'blue', 'text' => 'New'],
            'in_progress' => ['color' => 'yellow', 'text' => 'In Progress'],
            'responded' => ['color' => 'purple', 'text' => 'Responded'],
            'resolved' => ['color' => 'green', 'text' => 'Resolved'],
            'closed' => ['color' => 'gray', 'text' => 'Closed'],
            'spam' => ['color' => 'red', 'text' => 'Spam'],
        ];

        return $badges[$status] ?? ['color' => 'gray', 'text' => ucfirst($status)];
    }


    /**
     * Display the specified enquiry
     */
    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        return view('commerce::admin.enquiries.show', [
            'enquiry' => $enquiry
        ]);
    }

    /**
     * Update the status of the specified enquiry
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,contacted,converted',
            ]);

            $enquiry = Enquiry::findOrFail($id);
            $oldStatus = $enquiry->status;
            
            $enquiry->update([
                'status' => $validated['status']
            ]);

            Log::info('Enquiry status updated', [
                'enquiry_id' => $enquiry->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'updated_by' => auth('staff')->user()?->id,
            ]);

            return redirect()->back()->with('success', 'Enquiry status updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update enquiry status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update enquiry status.');
        }
    }

    /**
     * Remove the specified enquiry from storage
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $enquiry = Enquiry::findOrFail($id);
            
            Log::info('Enquiry deleted', [
                'enquiry_id' => $enquiry->id,
                'enquiry_name' => $enquiry->name,
                'enquiry_email' => $enquiry->email,
                'deleted_by' => auth('staff')->user()?->id,
            ]);

            $enquiry->delete();

            return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete enquiry: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete enquiry.');
        }
    }
}
