<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewVideoPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $video;

    /**
     * Create a new event instance.
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }
}
