<?php

namespace ElevateCommerce\Purchasable\Http\Controllers\Admin;

use ElevateCommerce\Purchasable\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number, email, or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(50);

        // Get stats for filters
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('purchasable::admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.purchasable',
            'addresses',
            'timeline.user'
        ]);

        return view('purchasable::admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'note' => 'nullable|string|max:1000',
        ]);

        $order->updateStatus(
            $request->status,
            $request->note,
            auth('admin')->id()
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'note' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->payment_status;
        $order->payment_status = $request->payment_status;
        $order->save();

        $order->logTimeline(
            'payment_status_changed',
            "Payment status changed from {$oldStatus} to {$request->payment_status}",
            $request->note,
            auth('admin')->id()
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Payment status updated successfully.');
    }

    /**
     * Update tracking number
     */
    public function updateTracking(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
            'shipping_method' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        $order->tracking_number = $request->tracking_number;
        if ($request->filled('shipping_method')) {
            $order->shipping_method = $request->shipping_method;
        }
        $order->save();

        $order->logTimeline(
            'tracking_updated',
            "Tracking number updated: {$request->tracking_number}",
            $request->note,
            auth('admin')->id()
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Tracking information updated successfully.');
    }

    /**
     * Add admin note to order
     */
    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $order->admin_note = $request->note;
        $order->save();

        $order->logTimeline(
            'admin_note_added',
            'Admin note added',
            $request->note,
            auth('admin')->id()
        );

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Note added successfully.');
    }
}
