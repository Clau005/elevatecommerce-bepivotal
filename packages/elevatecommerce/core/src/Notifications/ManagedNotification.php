<?php

namespace ElevateCommerce\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ManagedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification type
     */
    public string $type;

    /**
     * The notification data
     */
    public array $data;

    /**
     * Create a new notification instance
     */
    public function __construct(string $type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification
     */
    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting($this->getGreeting())
            ->line($this->getMessage());

        // Add action button if provided
        if (isset($this->data['action_url']) && isset($this->data['action_text'])) {
            $message->action($this->data['action_text'], $this->data['action_url']);
        }

        return $message;
    }

    /**
     * Get the array representation of the notification
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
            'message' => $this->getMessage(),
        ];
    }

    /**
     * Get the notification subject
     */
    protected function getSubject(): string
    {
        return match($this->type) {
            'order.created' => 'Order Confirmation - ' . ($this->data['order_number'] ?? ''),
            'order.updated' => 'Order Update - ' . ($this->data['order_number'] ?? ''),
            'order.processing' => 'Your Order is Being Processed',
            'order.shipped' => 'Your Order Has Been Shipped',
            'order.delivered' => 'Your Order Has Been Delivered',
            'order.cancelled' => 'Your Order Has Been Cancelled',
            'staff.order.new' => 'New Order Received - ' . ($this->data['order_number'] ?? ''),
            'staff.order.updated' => 'Order Status Changed - ' . ($this->data['order_number'] ?? ''),
            default => 'Notification from ' . config('app.name'),
        };
    }

    /**
     * Get the notification greeting
     */
    protected function getGreeting(): string
    {
        return 'Hello' . (isset($this->data['customer_name']) ? ' ' . $this->data['customer_name'] : '') . '!';
    }

    /**
     * Get the notification message
     */
    protected function getMessage(): string
    {
        return match($this->type) {
            'order.created' => 'Thank you for your order! We have received your order and will process it shortly.',
            'order.processing' => 'Your order is now being processed and will be shipped soon.',
            'order.shipped' => 'Great news! Your order has been shipped and is on its way to you.',
            'order.delivered' => 'Your order has been delivered. We hope you enjoy your purchase!',
            'order.cancelled' => 'Your order has been cancelled. If you have any questions, please contact us.',
            'staff.order.new' => 'A new order has been placed by ' . ($this->data['customer_name'] ?? 'a customer') . '.',
            'staff.order.updated' => 'Order status has been changed from ' . ($this->data['old_status'] ?? '') . ' to ' . ($this->data['new_status'] ?? '') . '.',
            default => $this->data['message'] ?? 'You have a new notification.',
        };
    }
}
