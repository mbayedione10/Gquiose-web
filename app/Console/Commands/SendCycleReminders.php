<?php

namespace App\Console\Commands;

use App\Models\CycleReminder;
use App\Models\MenstrualCycle;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendCycleReminders extends Command
{
    protected $signature = 'notifications:send-cycle-reminders';
    protected $description = 'Envoie les rappels automatiques pour les cycles menstruels';

    protected $notificationService;

    public function __construct(PushNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('Début de l\'envoi des rappels de cycle...');

        // Récupérer tous les rappels actifs
        $reminders = CycleReminder::where('enabled', true)
            ->with('utilisateur')
            ->get();

        $sent = 0;

        foreach ($reminders as $reminder) {
            $user = $reminder->utilisateur;

            if (!$user || !$user->fcm_token) {
                continue;
            }

            // Vérifier si on doit envoyer le rappel aujourd'hui
            if (!$this->shouldSendReminder($reminder, $user)) {
                continue;
            }

            // Créer la notification selon le type
            $notification = $this->createNotificationForReminder($reminder, $user);

            if ($notification) {
                try {
                    $this->notificationService->sendPushNotification($notification, [$user]);
                    $sent++;
                    $this->info("✓ Rappel envoyé à {$user->name} ({$reminder->reminder_type})");
                } catch (\Exception $e) {
                    Log::error("Erreur envoi rappel cycle user {$user->id}: " . $e->getMessage());
                    $this->error("✗ Erreur pour {$user->name}");
                }
            }
        }

        $this->info("✓ Total: {$sent} rappels de cycle envoyés");
        return 0;
    }

    /**
     * Vérifie si le rappel doit être envoyé aujourd'hui
     */
    protected function shouldSendReminder(CycleReminder $reminder, $user): bool
    {
        $now = Carbon::now();
        $reminderTime = Carbon::parse($reminder->reminder_time);

        // Vérifier si c'est l'heure du rappel (avec une marge de 5 minutes)
        if (abs($now->diffInMinutes($reminderTime)) > 5) {
            return false;
        }

        // Récupérer le cycle actif de l'utilisateur
        $activeCycle = MenstrualCycle::where('utilisateur_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$activeCycle) {
            return false;
        }

        switch ($reminder->reminder_type) {
            case 'period_start':
                // Rappel X jours avant le début prévu
                $daysBeforeArray = $reminder->days_before ?? [1];
                $nextPeriodDate = $activeCycle->next_period_date;
                
                if (!$nextPeriodDate) {
                    return false;
                }

                $daysUntilPeriod = Carbon::parse($nextPeriodDate)->diffInDays($now, false);
                
                return in_array(abs($daysUntilPeriod), $daysBeforeArray);

            case 'ovulation':
                // Rappel le jour de l'ovulation prévue
                $ovulationDate = $activeCycle->ovulation_date;
                
                if (!$ovulationDate) {
                    return false;
                }

                return Carbon::parse($ovulationDate)->isSameDay($now);

            case 'fertile_window':
                // Rappel pendant la fenêtre de fertilité
                $fertileStart = $activeCycle->fertile_window_start;
                $fertileEnd = $activeCycle->fertile_window_end;
                
                if (!$fertileStart || !$fertileEnd) {
                    return false;
                }

                return $now->between(
                    Carbon::parse($fertileStart)->startOfDay(),
                    Carbon::parse($fertileEnd)->endOfDay()
                ) && $now->isSameDay(Carbon::parse($fertileStart));

            case 'daily_log':
                // Rappel quotidien pour enregistrer les symptômes
                return true;

            default:
                return false;
        }
    }

    /**
     * Crée une notification push pour le rappel
     */
    protected function createNotificationForReminder(CycleReminder $reminder, $user): ?PushNotification
    {
        $activeCycle = MenstrualCycle::where('utilisateur_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$activeCycle) {
            return null;
        }

        $title = '';
        $message = '';
        $icon = 'calendar';
        $action = 'cycle';

        switch ($reminder->reminder_type) {
            case 'period_start':
                $daysUntil = Carbon::now()->diffInDays(Carbon::parse($activeCycle->next_period_date), false);
                if ($daysUntil == 0) {
                    $title = '🔴 Vos règles commencent aujourd\'hui';
                    $message = 'N\'oubliez pas de suivre votre cycle et vos symptômes.';
                } elseif ($daysUntil == 1) {
                    $title = '📅 Vos règles commencent demain';
                    $message = 'Préparez-vous, vos règles sont prévues demain.';
                } else {
                    $title = "📅 Règles dans {$daysUntil} jours";
                    $message = "Vos prochaines règles sont prévues dans {$daysUntil} jours.";
                }
                break;

            case 'ovulation':
                $title = '🥚 Période d\'ovulation';
                $message = 'Vous êtes dans votre période d\'ovulation. Fertilité maximale.';
                break;

            case 'fertile_window':
                $title = '💚 Fenêtre de fertilité';
                $message = 'Vous êtes dans votre fenêtre de fertilité. Bonne période pour concevoir.';
                break;

            case 'daily_log':
                $title = '📝 Enregistrez vos symptômes';
                $message = 'Prenez un moment pour enregistrer vos symptômes du jour.';
                break;

            default:
                return null;
        }

        return PushNotification::create([
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'action' => $action,
            'type' => 'cycle',
            'target_audience' => 'specific',
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
