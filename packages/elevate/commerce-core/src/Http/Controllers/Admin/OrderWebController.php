<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Order;
use Elevate\CommerceCore\Models\User;
use Elevate\CommerceCore\Models\Channel;
use Illuminate\Http\Request;

class OrderWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'channel']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('account_reference', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Channel filter
        if ($request->filled('channel_id')) {
            $query->where('channel_id', $request->channel_id);
        }

        // Customer type filter
        if ($request->filled('customer_type')) {
            if ($request->customer_type === 'new') {
                $query->whereHas('user', function ($q) {
                    $q->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
                });
            } elseif ($request->customer_type === 'returning') {
                $query->whereHas('user', function ($q) {
                    $q->whereRaw('created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)');
                });
            }
        }

        // Currency filter
        if ($request->filled('currency_code')) {
            $query->where('currency_code', $request->currency_code);
        }

        // Customer reference filter
        if ($request->filled('customer_reference')) {
            $query->where('customer_reference', 'like', '%' . $request->customer_reference . '%');
        }

        // Order reference filter
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        // Order total range filters
        if ($request->filled('min_total')) {
            $query->where('total', '>=', $request->min_total * 100); // Convert to cents
        }
        if ($request->filled('max_total')) {
            $query->where('total', '<=', $request->max_total * 100); // Convert to cents
        }

        // Date range filters
        if ($request->filled('placed_from')) {
            $query->where('created_at', '>=', $request->placed_from);
        }
        if ($request->filled('placed_to')) {
            $query->where('created_at', '<=', $request->placed_to);
        }

        // Has discounts filter
        if ($request->filled('has_discounts')) {
            $query->where('discount_total', '>', 0);
        }

        // Has notes filter
        if ($request->filled('has_notes')) {
            $query->whereNotNull('notes')->where('notes', '!=', '');
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort fields
        $allowedSorts = ['created_at', 'reference', 'status', 'total', 'customer_reference'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Get per_page from request, default to 4
        $perPage = $request->get('per_page', 20);
        
        // Server-side pagination with sorting
        $orders = $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
        $orders->appends($request->query());

        // Get additional data for filters
        $channels = Channel::select('id', 'name')->get();
        $currencies = Order::distinct()->pluck('currency_code')->filter()->sort()->values();

        // Prepare data for table
        $tableData = $orders->map(function($order) {
            // Determine customer info
            $customerName = 'Guest Customer';
            $customerEmail = 'N/A';
            
            if ($order->user) {
                $customerName = trim($order->user->first_name . ' ' . $order->user->last_name);
                $customerEmail = $order->user->email;
            } elseif (isset($order->meta['customer'])) {
                $customerName = $order->meta['customer']['name'] ?? 'Guest Customer';
                $customerEmail = $order->meta['customer']['email'] ?? 'N/A';
            }

            return [
                'id' => $order->id,
                'reference' => $order->reference,
                'customer_reference' => $order->customer_reference,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'status' => $order->status,
                'total' => $order->total,
                'items_count' => $order->lines()->count(),
                'created_at' => $order->created_at,
                'order_id' => $order->id,
            ];
        })->toArray();

        // Define columns with custom rendering
        $columns = [
            'reference' => [
                'label' => 'Order',
                'sortable' => true,
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['reference'].'</div>
                            <div class="text-sm text-gray-500">'.($row['customer_reference'] ?? '-').'</div>
                        </div>
                    ';
                }
            ],
            'customer_name' => [
                'label' => 'Customer',
                'render' => function($row) {
                    return '
                        <div>
                            <div class="text-sm font-medium text-gray-900">'.$row['customer_name'].'</div>
                            <div class="text-sm text-gray-500">'.$row['customer_email'].'</div>
                        </div>
                    ';
                }
            ],
            'status' => [
                'label' => 'Status',
                'sortable' => true,
                'render' => function($row) {
                    $colors = [
                        'awaiting-payment' => 'bg-yellow-100 text-yellow-800',
                        'payment-received' => 'bg-blue-100 text-blue-800',
                        'processing' => 'bg-purple-100 text-purple-800',
                        'shipped' => 'bg-indigo-100 text-indigo-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        'refunded' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $colors[$row['status']] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium '.$color.'">'.ucfirst(str_replace('-', ' ', $row['status'])).'</span>';
                }
            ],
            'total' => [
                'label' => 'Total',
                'sortable' => true,
                'render' => function($row) {
                    return '<div class="text-sm font-medium text-gray-900">£'.number_format($row['total'] / 100, 2).'</div>';
                }
            ],
            'items_count' => [
                'label' => 'Items',
                'render' => function($row) {
                    return '<div class="text-sm text-gray-500">'.$row['items_count'].'</div>';
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
                    return '
                        <a href="'.route('admin.orders.show', $row['order_id']).'" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View
                        </a>
                    ';
                }
            ],
        ];

        return view('commerce::admin.orders.index', [
            'data' => $tableData,
            'columns' => $columns,
            'orders' => $orders,
            'channels' => $channels,
            'currencies' => $currencies,
        ]);
    }

    public function show(Order $order)
    {
        // Load all necessary relationships
        $order->load([
            'user', 
            'channel', 
            'lines.purchasable', 
            'addresses',
            'payments',
            'discountUsages.discount',
            'timelines' => function ($query) {
                $query->with(['user', 'staff'])->orderBy('created_at', 'desc');
            }
        ]);

        // Calculate order statistics
        $stats = [
            'items_count' => $order->lines->sum('quantity'),
            'discount_amount' => ($order->discount_total ?? 0) / 100,
            'gift_voucher_amount' => ($order->gift_voucher_total ?? 0) / 100,
            'total_savings' => (($order->discount_total ?? 0) + ($order->gift_voucher_total ?? 0)) / 100,
            'tax_amount' => $order->tax_total / 100,
            'shipping_amount' => collect($order->shipping_breakdown)->sum('total') / 100 ?? 0,
            'subtotal' => $order->sub_total / 100,
            'total' => $order->total / 100,
        ];

        // Get addresses
        $billingAddress = $order->billingAddress();
        $shippingAddress = $order->shippingAddress();

        // Get payment information
        $payments = $order->payments()->orderBy('created_at', 'desc')->get();

        return view('commerce::admin.orders.show', compact(
            'order', 
            'stats', 
            'billingAddress', 
            'shippingAddress', 
            'payments'
        ));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:awaiting-payment,payment-received,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:1000',
            'customer_reference' => 'nullable|string|max:255',
        ]);

        // Only update fields that are present in the request
        $updateData = array_filter($validated, function($value) {
            return $value !== null;
        });

        $order->update($updateData);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    public function storeTimelineComment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_customer_visible' => 'required|boolean',
        ]);

        $timeline = $order->timelines()->create([
            'type' => 'comment',
            'title' => '',
            'content' => $validated['content'],
            'staff_id' => auth('staff')->id(),
            'is_system_event' => false,
            'is_visible_to_customer' => $validated['is_customer_visible'],
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Comment added successfully.');
    }

    public function updateTimelineComment(Request $request, Order $order, $timelineId)
    {
        $timeline = $order->timelines()->where('id', $timelineId)->where('is_system_event', false)->firstOrFail();

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_customer_visible' => 'required|boolean',
        ]);

        $timeline->update([
            'content' => $validated['content'],
            'is_visible_to_customer' => $validated['is_customer_visible'],
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Comment updated successfully.');
    }

    public function destroyTimelineComment(Order $order, $timelineId)
    {
        $timeline = $order->timelines()->where('id', $timelineId)->where('is_system_event', false)->firstOrFail();
        $timeline->delete();

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Comment deleted successfully.');
    }
}
