<?php

namespace ElevateCommerce\Core\Support\Notifications;

use ElevateCommerce\Core\Models\Admin;
use ElevateCommerce\Core\Notifications\ManagedNotification;
use Illuminate\Support\Facades\Notification;

class NotificationManager
{
    /**
     * Send notification to a user
     */
    public static function send(string $type, $data, $notifiable): void
    {
        if (is_array($data)) {
            $notificationData = $data;
        } else {
            // If $data is an object (like Order), convert to array
            $notificationData = method_exists($data, 'toArray') ? $data->toArray() : (array) $data;
        }

        $notifiable->notify(new ManagedNotification($type, $notificationData));
    }

    /**
     * Send notification to all staff/admins
     */
    public static function sendToStaff(string $type, array $data): void
    {
        $admins = Admin::all();
        
        Notification::send($admins, new ManagedNotification($type, $data));
    }

    /**
     * Send notification to specific email (for guest orders)
     */
    public static function sendToEmail(string $email, string $type, array $data): void
    {
        Notification::route('mail', $email)
            ->notify(new ManagedNotification($type, $data));
    }
}
