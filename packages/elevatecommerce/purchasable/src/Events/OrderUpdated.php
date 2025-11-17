<?php

namespace ElevateCommerce\Purchasable\Events;

use ElevateCommerce\Purchasable\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * The order instance
     */
    public Order $order;

    /**
     * The old status (if status changed)
     */
    public ?string $oldStatus;

    /**
     * The new status (if status changed)
     */
    public ?string $newStatus;

    /**
     * Create a new event instance
     */
    public function __construct(Order $order, ?string $oldStatus = null, ?string $newStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
