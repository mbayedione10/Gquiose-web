<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérifier les notifications programmées toutes les minutes
        $schedule->command('notifications:send-scheduled')->everyMinute();

        // Envoyer un conseil santé chaque lundi à 9h00 (GMT Guinée)
        $schedule->command('notifications:send-weekly-health-tips')
            ->weeklyOn(1, '09:00');

        // Envoyer les rappels de cycle menstruel toutes les heures
        $schedule->command('notifications:send-cycle-reminders')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
