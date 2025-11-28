
<?php

namespace App\Events;

use App\Models\Utilisateur;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CycleReminderTriggered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $reminderType; // 'period_coming', 'ovulation', 'fertile_window'
    public $daysUntil;

    public function __construct(Utilisateur $user, string $reminderType, int $daysUntil)
    {
        $this->user = $user;
        $this->reminderType = $reminderType;
        $this->daysUntil = $daysUntil;
    }
}
