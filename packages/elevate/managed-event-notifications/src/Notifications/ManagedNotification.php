<?php

namespace Elevate\ManagedEventNotifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManagedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $type;
    protected mixed $data;
    protected array $config;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, mixed $data, array $config)
    {
        $this->type = $type;
        $this->data = $data;
        $this->config = $config;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->config['channels'] ?? ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->parseSubject();
        $template = $this->config['template'];

        return (new MailMessage)
            ->subject($subject)
            ->view("managed-notifications::{$template}", [
                'notifiable' => $notifiable,
                'data' => $this->data,
                'type' => $this->type,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'subject' => $this->parseSubject(),
            'data' => $this->serializeData(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Parse the subject line with data placeholders
     */
    protected function parseSubject(): string
    {
        $subject = $this->config['subject'] ?? 'Notification';

        // Replace placeholders like :number, :name, etc.
        if (is_object($this->data)) {
            foreach (get_object_vars($this->data) as $key => $value) {
                if (is_scalar($value)) {
                    $subject = str_replace(":{$key}", (string) $value, $subject);
                }
            }
        } elseif (is_array($this->data)) {
            foreach ($this->data as $key => $value) {
                if (is_scalar($value)) {
                    $subject = str_replace(":{$key}", (string) $value, $subject);
                }
            }
        }

        return $subject;
    }

    /**
     * Serialize data for database storage
     */
    protected function serializeData(): array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        if (is_object($this->data)) {
            // If it's an Eloquent model, return key attributes
            if (method_exists($this->data, 'toArray')) {
                return $this->data->toArray();
            }

            return get_object_vars($this->data);
        }

        return ['value' => $this->data];
    }
}
