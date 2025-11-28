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
        // Envoyer les rappels de cycle quotidiennement à 9h00
        $schedule->command('cycle:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Africa/Conakry');

        // Envoyer les notifications programmées toutes les 5 minutes
        $schedule->command('notifications:send-scheduled')
            ->everyFiveMinutes();

        // Envoyer les conseils de santé hebdomadaires le lundi à 10h00
        $schedule->command('health-tips:send-weekly')
            ->weeklyOn(1, '10:00')
            ->timezone('Africa/Conakry');
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