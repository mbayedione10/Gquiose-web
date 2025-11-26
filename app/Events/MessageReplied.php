<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReplied
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Chat $chat;
    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Chat $chat, Message $message)
    {
        $this->chat = $chat;
        $this->message = $message;
    }
}
