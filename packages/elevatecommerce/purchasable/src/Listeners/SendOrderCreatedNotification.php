<?php

namespace ElevateCommerce\Purchasable\Listeners;

use ElevateCommerce\Core\Support\Notifications\NotificationManager;
use ElevateCommerce\Purchasable\Events\OrderCreated;

class SendOrderCreatedNotification
{
    /**
     * Handle the event
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        // Send notification to staff about new order
        NotificationManager::sendToStaff('staff.order.new', [
            'order_number' => $order->order_number,
            'customer_name' => $order->metadata['billing_name'] ?? $order->guest_email,
            'customer_email' => $order->user?->email ?? $order->guest_email,
            'total' => $order->total,
            'currency' => $order->currency_code,
            'items_count' => $order->items->count(),
            'action_url' => route('admin.orders.show', $order),
            'action_text' => 'View Order',
        ]);

        // Send order confirmation to customer
        if ($order->user) {
            NotificationManager::send('order.created', [
                'order_number' => $order->order_number,
                'customer_name' => $order->user->first_name,
                'total' => $order->total,
                'currency' => $order->currency_code,
                'action_url' => route('admin.orders.show', $order),
                'action_text' => 'View Order',
            ], $order->user);
        } elseif ($order->guest_email) {
            // For guest orders, send to email directly
            NotificationManager::sendToEmail($order->guest_email, 'order.created', [
                'order_number' => $order->order_number,
                'customer_name' => $order->metadata['billing_name'] ?? 'Customer',
                'total' => $order->total,
                'currency' => $order->currency_code,
            ]);
        }
    }
}
