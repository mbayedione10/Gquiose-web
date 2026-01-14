<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Paginator::useBootstrap();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Créer les préférences de notification par défaut pour les nouveaux utilisateurs
        \App\Models\Utilisateur::created(function ($user) {
            \App\Models\UserNotificationPreference::create([
                'utilisateur_id' => $user->id,
                'notifications_enabled' => true,
                'cycle_notifications' => true,
                'content_notifications' => true,
                'forum_notifications' => true,
                'health_tips_notifications' => true,
                'admin_notifications' => true,
                'do_not_disturb' => false,
            ]);
        });
    }
}
