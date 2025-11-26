<?php

namespace App\Events;

use App\Models\Thematique;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewQuizPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thematique;

    /**
     * Create a new event instance.
     */
    public function __construct(Thematique $thematique)
    {
        $this->thematique = $thematique;
    }
}
