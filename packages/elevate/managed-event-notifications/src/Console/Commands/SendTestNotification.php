<?php

namespace Elevate\ManagedEventNotifications\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Elevate\ManagedEventNotifications\Facades\ManagedNotifications;

class SendTestNotification extends Command
{
    protected $signature = 'notifications:test {type} {email}';
    protected $description = 'Send a test notification';

    public function handle(): void
    {
        $type = $this->argument('type');
        $email = $this->argument('email');

        // Check if notification type exists
        if (!ManagedNotifications::isEnabled($type)) {
            $this->error("Notification type '{$type}' is not enabled or does not exist.");
            $this->info('Use notifications:list to see available notifications.');
            return;
        }

        // Create test data
        $testData = $this->getTestData($type);

        // Send notification
        try {
            Notification::route('mail', $email)->notify(
                new \Elevate\ManagedEventNotifications\Notifications\ManagedNotification(
                    $type,
                    $testData,
                    config("managed-notifications.customer_notifications.{$type}") 
                        ?? config("managed-notifications.staff_notifications.{$type}")
                )
            );

            $this->info("Test notification sent to {$email}");
        } catch (\Exception $e) {
            $this->error("Failed to send notification: {$e->getMessage()}");
        }
    }

    protected function getTestData(string $type): object
    {
        // Create mock data based on notification type
        return (object) [
            'number' => '1234',
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'total' => '$99.99',
            'items' => 3,
            'tracking_number' => 'TRACK123456',
        ];
    }
}
