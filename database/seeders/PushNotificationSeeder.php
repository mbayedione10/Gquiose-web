
<?php

namespace Database\Seeders;

use App\Models\PushNotification;
use Illuminate\Database\Seeder;

class PushNotificationSeeder extends Seeder
{
    public function run()
    {
        $notifications = [
            [
                'title' => 'Rappel de cycle',
                'message' => 'Votre période devrait commencer dans 3 jours',
                'icon' => '🩸',
                'type' => 'automatic',
                'target_audience' => 'all',
                'status' => 'sent',
                'sent_count' => 150,
                'delivered_count' => 145,
                'opened_count' => 98,
                'clicked_count' => 45,
                'sent_at' => now()->subDays(5),
            ],
            [
                'title' => 'Nouveau conseil santé',
                'message' => 'Découvrez nos conseils pour une alimentation équilibrée pendant votre cycle',
                'icon' => '💡',
                'type' => 'manual',
                'target_audience' => 'filtered',
                'filters' => ['age_min' => 18, 'age_max' => 35],
                'status' => 'sent',
                'sent_count' => 200,
                'delivered_count' => 195,
                'opened_count' => 120,
                'clicked_count' => 80,
                'sent_at' => now()->subDays(3),
            ],
            [
                'title' => 'Nouvel article',
                'message' => 'Un nouvel article sur la santé reproductive est disponible',
                'icon' => '📚',
                'type' => 'automatic',
                'target_audience' => 'all',
                'status' => 'sent',
                'sent_count' => 300,
                'delivered_count' => 290,
                'opened_count' => 180,
                'clicked_count' => 95,
                'sent_at' => now()->subDays(2),
            ],
            [
                'title' => 'Quiz du jour',
                'message' => 'Testez vos connaissances sur la santé reproductive',
                'icon' => '❓',
                'type' => 'scheduled',
                'target_audience' => 'all',
                'status' => 'sent',
                'sent_count' => 250,
                'delivered_count' => 240,
                'opened_count' => 160,
                'clicked_count' => 110,
                'scheduled_at' => now()->subDays(1)->setHour(10),
                'sent_at' => now()->subDays(1),
            ],
            [
                'title' => 'Alerte confirmée',
                'message' => 'Votre signalement a été pris en compte par nos équipes',
                'icon' => '✅',
                'type' => 'automatic',
                'target_audience' => 'filtered',
                'filters' => ['has_alerts' => true],
                'status' => 'sent',
                'sent_count' => 50,
                'delivered_count' => 48,
                'opened_count' => 45,
                'clicked_count' => 30,
                'sent_at' => now()->subHours(12),
            ],
            [
                'title' => 'Nouvelle vidéo',
                'message' => 'Une nouvelle vidéo éducative est disponible',
                'icon' => '🎥',
                'type' => 'manual',
                'target_audience' => 'all',
                'status' => 'pending',
            ],
            [
                'title' => 'Conseil du jour',
                'message' => 'Prenez soin de votre santé mentale et physique',
                'icon' => '🩺',
                'type' => 'scheduled',
                'target_audience' => 'all',
                'status' => 'pending',
                'scheduled_at' => now()->addDays(1)->setHour(9),
            ],
            [
                'title' => 'Suivi de grossesse',
                'message' => 'N\'oubliez pas votre consultation prénatale cette semaine',
                'icon' => '🤰',
                'type' => 'automatic',
                'target_audience' => 'filtered',
                'filters' => ['has_cycle_data' => true],
                'status' => 'pending',
            ],
            [
                'title' => 'Centre de santé à proximité',
                'message' => 'Un nouveau centre de santé a été ajouté près de chez vous',
                'icon' => '🏥',
                'type' => 'manual',
                'target_audience' => 'filtered',
                'filters' => ['ville_id' => 1],
                'status' => 'pending',
            ],
            [
                'title' => 'Message important',
                'message' => 'Vous avez reçu un nouveau message dans le forum',
                'icon' => '💬',
                'type' => 'automatic',
                'target_audience' => 'all',
                'status' => 'pending',
            ],
        ];

        foreach ($notifications as $notification) {
            PushNotification::create($notification);
        }

        $this->command->info('10 notifications push créées avec succès!');
    }
}
