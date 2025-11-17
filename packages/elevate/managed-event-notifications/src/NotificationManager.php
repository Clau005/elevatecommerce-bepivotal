<?php

namespace Elevate\ManagedEventNotifications;

use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Foundation\Application;
use Elevate\ManagedEventNotifications\Notifications\ManagedNotification;

class NotificationManager
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Send a customer notification
     *
     * @param string $type Notification type (e.g., 'order.confirmation')
     * @param mixed $data Data to pass to the notification
     * @param mixed $notifiable The notifiable entity (customer)
     * @return void
     */
    public function send(string $type, $data, $notifiable): void
    {
        $config = config("managed-notifications.customer_notifications.{$type}");

        if (!$config || !($config['enabled'] ?? false)) {
            return;
        }

        $notification = new ManagedNotification($type, $data, $config);

        if (config('managed-notifications.queue')) {
            Notification::send($notifiable, $notification);
        } else {
            Notification::sendNow($notifiable, $notification);
        }
    }

    /**
     * Send a staff notification
     *
     * @param string $type Notification type (e.g., 'staff.order.new')
     * @param mixed $data Data to pass to the notification
     * @return void
     */
    public function sendToStaff(string $type, $data): void
    {
        $config = config("managed-notifications.staff_notifications.{$type}");

        if (!$config || !($config['enabled'] ?? false)) {
            return;
        }

        $recipients = $this->getStaffRecipients($config['recipients'] ?? 'all_orders');

        if (empty($recipients)) {
            return;
        }

        $notification = new ManagedNotification($type, $data, $config);

        foreach ($recipients as $recipient) {
            if (config('managed-notifications.queue')) {
                Notification::route('mail', $recipient)->notify($notification);
            } else {
                Notification::route('mail', $recipient)->notifyNow($notification);
            }
        }
    }

    /**
     * Get staff recipients based on recipient type
     *
     * @param string $recipientType
     * @return array
     */
    protected function getStaffRecipients(string $recipientType): array
    {
        $recipients = config("managed-notifications.staff_recipients.{$recipientType}", []);

        // Filter out empty values
        return array_filter($recipients, fn($email) => !empty($email));
    }

    /**
     * Check if a notification type is enabled
     *
     * @param string $type
     * @return bool
     */
    public function isEnabled(string $type): bool
    {
        // Check customer notifications
        $customerConfig = config("managed-notifications.customer_notifications.{$type}");
        if ($customerConfig && ($customerConfig['enabled'] ?? false)) {
            return true;
        }

        // Check staff notifications
        $staffConfig = config("managed-notifications.staff_notifications.{$type}");
        if ($staffConfig && ($staffConfig['enabled'] ?? false)) {
            return true;
        }

        return false;
    }

    /**
     * Get all available notification types
     *
     * @return array
     */
    public function getAvailableNotifications(): array
    {
        return [
            'customer' => array_keys(config('managed-notifications.customer_notifications', [])),
            'staff' => array_keys(config('managed-notifications.staff_notifications', [])),
        ];
    }
}
