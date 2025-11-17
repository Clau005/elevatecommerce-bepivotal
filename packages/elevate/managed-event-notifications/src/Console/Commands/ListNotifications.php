<?php

namespace Elevate\ManagedEventNotifications\Console\Commands;

use Illuminate\Console\Command;

class ListNotifications extends Command
{
    protected $signature = 'notifications:list';
    protected $description = 'List all available managed notifications';

    public function handle(): void
    {
        $this->info('Customer Notifications:');
        $this->table(
            ['Type', 'Enabled', 'Subject', 'Channels'],
            $this->getNotificationRows('customer_notifications')
        );

        $this->newLine();
        $this->info('Staff Notifications:');
        $this->table(
            ['Type', 'Enabled', 'Subject', 'Channels'],
            $this->getNotificationRows('staff_notifications')
        );
    }

    protected function getNotificationRows(string $type): array
    {
        $notifications = config("managed-notifications.{$type}", []);
        $rows = [];

        foreach ($notifications as $key => $config) {
            $rows[] = [
                $key,
                $config['enabled'] ? '✓' : '✗',
                $config['subject'] ?? 'N/A',
                implode(', ', $config['channels'] ?? []),
            ];
        }

        return $rows;
    }
}
