<?php

namespace App\Events;

use App\Models\Inventory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $inventory;

    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function broadcastOn()
    {
        return new Channel('inventory-updates');
    }

    public function broadcastWith()
    {
        return ['inventory' => $this->inventory];
    }
}
