<?php

namespace ElevateCommerce\Purchasable\Events;

use ElevateCommerce\Purchasable\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels;

    /**
     * The order instance
     */
    public Order $order;

    /**
     * Create a new event instance
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
