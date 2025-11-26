<?php

namespace App\Events;

use App\Models\Utilisateur;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMentioned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Utilisateur $mentionedUser;
    public Utilisateur $mentioner;
    public string $content;
    public string $context;
    public int $contextId;

    /**
     * Create a new event instance.
     *
     * @param Utilisateur $mentionedUser The user being mentioned
     * @param Utilisateur $mentioner The user who mentioned
     * @param string $content The message content
     * @param string $context Either 'message' or 'chat'
     * @param int $contextId The ID of the message or chat
     */
    public function __construct(
        Utilisateur $mentionedUser,
        Utilisateur $mentioner,
        string $content,
        string $context,
        int $contextId
    ) {
        $this->mentionedUser = $mentionedUser;
        $this->mentioner = $mentioner;
        $this->content = $content;
        $this->context = $context;
        $this->contextId = $contextId;
    }
}
