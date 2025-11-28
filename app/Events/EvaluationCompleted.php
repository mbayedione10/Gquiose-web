<?php

namespace App\Events;

use App\Models\Evaluation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EvaluationCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $evaluation;

    /**
     * Create a new event instance.
     */
    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }
}
