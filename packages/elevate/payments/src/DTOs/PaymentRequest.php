<?php

namespace Elevate\Payments\DTOs;

class PaymentRequest
{
    public function __construct(
        public float $amount,
        public string $currency,
        public array $metadata = [],
        public ?string $returnUrl = null,
        public ?string $cancelUrl = null,
    ) {}
}
