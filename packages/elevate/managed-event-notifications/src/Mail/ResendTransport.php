<?php

namespace Elevate\ManagedEventNotifications\Mail;

use Illuminate\Mail\Transport\Transport;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Resend;

class ResendTransport extends Transport
{
    protected string $apiKey;
    protected ?Resend $client = null;

    public function __construct(string $apiKey)
    {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    /**
     * Get the Resend client
     */
    protected function getClient(): Resend
    {
        if ($this->client === null) {
            $this->client = Resend::client($this->apiKey);
        }

        return $this->client;
    }

    /**
     * Send the message
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $payload = [
            'from' => $this->getFrom($email),
            'to' => $this->getTo($email),
            'subject' => $email->getSubject(),
        ];

        // Add HTML body
        if ($email->getHtmlBody()) {
            $payload['html'] = $email->getHtmlBody();
        }

        // Add text body
        if ($email->getTextBody()) {
            $payload['text'] = $email->getTextBody();
        }

        // Add CC
        if ($cc = $email->getCc()) {
            $payload['cc'] = array_map(fn($address) => $address->getAddress(), $cc);
        }

        // Add BCC
        if ($bcc = $email->getBcc()) {
            $payload['bcc'] = array_map(fn($address) => $address->getAddress(), $bcc);
        }

        // Add Reply-To
        if ($replyTo = $email->getReplyTo()) {
            $payload['reply_to'] = array_map(fn($address) => $address->getAddress(), $replyTo);
        }

        // Add attachments
        if ($attachments = $email->getAttachments()) {
            $payload['attachments'] = [];
            foreach ($attachments as $attachment) {
                $payload['attachments'][] = [
                    'filename' => $attachment->getFilename() ?? 'attachment',
                    'content' => base64_encode($attachment->getBody()),
                ];
            }
        }

        // Send via Resend
        try {
            $this->getClient()->emails->send($payload);
        } catch (\Exception $e) {
            throw new \Exception("Resend API error: " . $e->getMessage());
        }
    }

    /**
     * Get the "from" address
     */
    protected function getFrom($email): string
    {
        $from = $email->getFrom();
        
        if (empty($from)) {
            return config('managed-notifications.resend.from.address');
        }

        $address = $from[0];
        
        if ($address->getName()) {
            return sprintf('%s <%s>', $address->getName(), $address->getAddress());
        }

        return $address->getAddress();
    }

    /**
     * Get the "to" addresses
     */
    protected function getTo($email): array
    {
        return array_map(
            fn($address) => $address->getAddress(),
            $email->getTo()
        );
    }

    /**
     * Get the string representation of the transport
     */
    public function __toString(): string
    {
        return 'resend';
    }
}
