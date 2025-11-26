<?php

namespace App\Events;

use App\Models\Structure;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewHealthCenterAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $structure;

    /**
     * Create a new event instance.
     */
    public function __construct(Structure $structure)
    {
        $this->structure = $structure;
    }
}
