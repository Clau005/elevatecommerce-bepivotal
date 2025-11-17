<?php

namespace Elevate\ManagedEventNotifications\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void send(string $type, mixed $data, mixed $notifiable)
 * @method static void sendToStaff(string $type, mixed $data)
 * @method static bool isEnabled(string $type)
 * @method static array getAvailableNotifications()
 *
 * @see \Elevate\ManagedEventNotifications\NotificationManager
 */
class ManagedNotifications extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'managed-notifications';
    }
}
