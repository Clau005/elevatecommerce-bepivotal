<?php

namespace Elevate\Payments\DTOs;

class PaymentResponse
{
    public function __construct(
        public bool $success,
        public ?string $paymentId = null,
        public ?string $redirectUrl = null,
        public ?string $status = null,
        public ?array $data = null,
        public ?string $error = null,
    ) {}
    
    /**
     * Check if payment was successful
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }
    
    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return !$this->success;
    }
}
