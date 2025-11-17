<?php

namespace Elevate\ManagedEventNotifications\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Elevate\ManagedEventNotifications\NotificationManager;
use Elevate\ManagedEventNotifications\Notifications\ManagedNotification;

class NotificationController extends Controller
{
    protected NotificationManager $manager;

    public function __construct(NotificationManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Display a listing of all notifications
     */
    public function index()
    {
        $customerNotifications = config('managed-notifications.customer_notifications', []);
        $staffNotifications = config('managed-notifications.staff_notifications', []);

        return view('managed-notifications::admin.index', compact(
            'customerNotifications',
            'staffNotifications'
        ));
    }

    /**
     * Show notification settings
     */
    public function settings()
    {
        $config = config('managed-notifications');
        
        return view('managed-notifications::admin.settings', compact('config'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'notifications' => 'array',
            'notifications.*.enabled' => 'boolean',
        ]);

        // In a real implementation, you'd save these to a database
        // or update the config file programmatically
        
        return redirect()
            ->route('admin.managed-notifications.settings')
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Preview a notification template
     */
    public function preview(Request $request, string $type)
    {
        $notificationType = str_replace('.', '/', $type);
        $isStaff = str_starts_with($type, 'staff.');
        
        $config = $isStaff 
            ? config("managed-notifications.staff_notifications.{$type}")
            : config("managed-notifications.customer_notifications.{$type}");

        if (!$config) {
            abort(404, 'Notification type not found');
        }

        // Generate sample data based on notification type
        $sampleData = $this->getSampleData($type);
        $sampleNotifiable = $this->getSampleNotifiable($isStaff);

        // Render the template
        $template = $config['template'];
        
        try {
            $html = View::make("managed-notifications::{$template}", [
                'notifiable' => $sampleNotifiable,
                'data' => $sampleData,
                'type' => $type,
            ])->render();

            return view('managed-notifications::admin.preview', compact(
                'type',
                'config',
                'html',
                'sampleData'
            ));
        } catch (\Exception $e) {
            return view('managed-notifications::admin.preview-error', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a test notification
     */
    public function sendTest(Request $request, string $type)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $isStaff = str_starts_with($type, 'staff.');
        
        $config = $isStaff 
            ? config("managed-notifications.staff_notifications.{$type}")
            : config("managed-notifications.customer_notifications.{$type}");

        if (!$config) {
            return response()->json(['error' => 'Notification type not found'], 404);
        }

        $sampleData = $this->getSampleData($type);

        try {
            Notification::route('mail', $validated['email'])->notify(
                new ManagedNotification($type, $sampleData, $config)
            );

            return response()->json([
                'success' => true,
                'message' => "Test notification sent to {$validated['email']}",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send test notification: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show notification history
     */
    public function history(Request $request)
    {
        // This would query the notifications table
        $notifications = DB::table('notifications')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('managed-notifications::admin.history', compact('notifications'));
    }

    /**
     * Get sample data for a notification type
     */
    protected function getSampleData(string $type): object
    {
        $baseData = [
            'number' => '1234',
            'created_at' => now(),
            'total' => '$99.99',
            'order_url' => url('/orders/1234'),
            'admin_order_url' => url('/admin/orders/1234'),
        ];

        // Customize based on notification type
        $customData = match(true) {
            str_contains($type, 'shipping') => [
                'tracking_number' => 'TRACK123456789',
                'tracking_url' => 'https://tracking.example.com/TRACK123456789',
                'estimated_delivery' => now()->addDays(3)->format('F j, Y'),
                'shipping_method' => 'Standard Shipping',
            ],
            str_contains($type, 'pickup') => [
                'pickup_location' => '123 Main St, City, State 12345',
                'pickup_hours' => 'Monday-Friday: 9 AM - 5 PM',
                'pickup_instructions' => 'Please bring a valid ID and your order number.',
            ],
            str_contains($type, 'delivery') => [
                'estimated_delivery_time' => '2-4 PM',
                'delivery_address' => '456 Oak Ave, City, State 67890',
                'delivered_to' => 'John Doe',
                'next_attempt' => now()->addDays(1)->format('F j, Y'),
            ],
            str_contains($type, 'gift_card') => [
                'code' => 'GIFT-' . strtoupper(substr(md5(time()), 0, 8)),
                'balance' => '$50.00',
                'sender_name' => 'Jane Smith',
                'message' => 'Happy Birthday! Enjoy your gift!',
                'shop_url' => url('/shop'),
            ],
            str_contains($type, 'store_credit') => [
                'amount' => '$25.00',
                'new_balance' => '$75.00',
                'reason' => 'Refund for order #1234',
            ],
            str_contains($type, 'payment.failure') => [
                'error_message' => 'Insufficient funds',
                'payment_method' => 'Visa ending in 4242',
                'attempt_number' => '2',
            ],
            str_contains($type, 'inventory.failure') => [
                'out_of_stock_items' => [
                    (object)['name' => 'Product A', 'quantity' => 2, 'available' => 0],
                    (object)['name' => 'Product B', 'quantity' => 1, 'available' => 0],
                ],
            ],
            default => [],
        };

        // Add common order items
        if (str_contains($type, 'order') || str_contains($type, 'invoice')) {
            $customData['items'] = [
                (object)[
                    'name' => 'Sample Product 1',
                    'quantity' => 2,
                    'price' => '$29.99',
                ],
                (object)[
                    'name' => 'Sample Product 2',
                    'quantity' => 1,
                    'price' => '$39.99',
                ],
            ];
            $customData['items_count'] = 2;
        }

        // Add customer info for staff notifications
        if (str_starts_with($type, 'staff.')) {
            $customData['customer_name'] = 'John Doe';
            $customData['customer_email'] = 'john.doe@example.com';
        }

        return (object) array_merge($baseData, $customData);
    }

    /**
     * Get sample notifiable entity
     */
    protected function getSampleNotifiable(bool $isStaff): object
    {
        return (object) [
            'name' => $isStaff ? 'Admin User' : 'John Doe',
            'email' => $isStaff ? 'admin@example.com' : 'john.doe@example.com',
        ];
    }
}
