<?php

namespace ElevateCommerce\Purchasable\Listeners;

use ElevateCommerce\Core\Support\Notifications\NotificationManager;
use ElevateCommerce\Purchasable\Events\OrderUpdated;

class SendOrderUpdatedNotification
{
    /**
     * Handle the event
     */
    public function handle(OrderUpdated $event): void
    {
        $order = $event->order;

        // Only send notifications for significant status changes
        if ($event->oldStatus && $event->newStatus && $event->oldStatus !== $event->newStatus) {
            // Notify staff about status change
            NotificationManager::sendToStaff('staff.order.updated', [
                'order_number' => $order->order_number,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'customer_name' => $order->metadata['billing_name'] ?? $order->guest_email,
                'action_url' => route('admin.orders.show', $order),
                'action_text' => 'View Order',
            ]);

            // Send customer notification based on new status
            $this->sendCustomerNotification($order, $event->newStatus);
        }
    }

    /**
     * Send appropriate customer notification based on status
     */
    protected function sendCustomerNotification($order, string $status): void
    {
        $notificationType = match($status) {
            'processing' => 'order.processing',
            'shipped' => 'order.shipped',
            'delivered' => 'order.delivered',
            'cancelled' => 'order.cancelled',
            default => null,
        };

        if (!$notificationType) {
            return;
        }

        $notificationData = [
            'order_number' => $order->order_number,
            'customer_name' => $order->metadata['billing_name'] ?? 'Customer',
            'total' => $order->total,
            'currency' => $order->currency_code,
        ];

        if ($order->user) {
            NotificationManager::send($notificationType, $notificationData, $order->user);
        } elseif ($order->guest_email) {
            NotificationManager::sendToEmail($order->guest_email, $notificationType, $notificationData);
        }
    }
}
