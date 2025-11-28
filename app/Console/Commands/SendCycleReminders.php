<?php

namespace App\Console\Commands;

use App\Events\CycleReminderTriggered;
use App\Models\MenstrualCycle;
use App\Models\Utilisateur;
use Illuminate\Console\Command;

class SendCycleReminders extends Command
{
    protected $signature = 'cycle:send-reminders';
    protected $description = 'Envoie les rappels de cycle menstruel basés sur les prédictions';

    public function handle()
    {
        $this->info('Vérification des cycles pour les rappels...');

        // Récupérer tous les utilisateurs avec des données de cycle
        $users = Utilisateur::whereHas('menstrualCycles')
            ->where('status', true)
            ->whereNotNull('fcm_token')
            ->get();

        $remindersSent = 0;

        foreach ($users as $user) {
            // Vérifier les préférences de notification
            $preferences = $user->notificationPreferences;
            if (!$preferences || !$preferences->cycle_notifications || !$preferences->notifications_enabled) {
                continue;
            }

            // Récupérer le dernier cycle
            $latestCycle = $user->menstrualCycles()
                ->orderBy('start_date', 'desc')
                ->first();

            if (!$latestCycle || !$latestCycle->next_period_date) {
                continue;
            }

            $nextPeriodDate = $latestCycle->next_period_date;
            $daysUntil = now()->diffInDays($nextPeriodDate, false);

            // Rappel 3 jours avant les règles
            if ($daysUntil == 3) {
                event(new CycleReminderTriggered($user, 'period_coming', 3));
                $remindersSent++;
                $this->info("Rappel envoyé à {$user->email} (règles dans 3 jours)");
            }

            // Rappel le jour des règles
            if ($daysUntil == 0) {
                event(new CycleReminderTriggered($user, 'period_coming', 0));
                $remindersSent++;
                $this->info("Rappel envoyé à {$user->email} (règles aujourd'hui)");
            }

            // Rappel ovulation (environ 14 jours avant les prochaines règles)
            if ($latestCycle->ovulation_date && now()->isSameDay($latestCycle->ovulation_date)) {
                event(new CycleReminderTriggered($user, 'ovulation', 0));
                $remindersSent++;
                $this->info("Rappel ovulation envoyé à {$user->email}");
            }
        }

        $this->info("Total: {$remindersSent} rappels de cycle envoyés");
        return 0;
    }
}