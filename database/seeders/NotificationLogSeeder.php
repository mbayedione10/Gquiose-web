
<?php

namespace Database\Seeders;

use App\Models\NotificationLog;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class NotificationLogSeeder extends Seeder
{
    public function run()
    {
        $utilisateurs = Utilisateur::all();

        if ($utilisateurs->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Veuillez d\'abord exécuter UtilisateurSeeder.');
            return;
        }

        $types = ['manual', 'automatic', 'scheduled'];
        $categories = ['alert', 'reminder', 'health_tip', 'cycle', 'general', 'quiz', 'article', 'video'];
        $statuses = ['sent', 'delivered', 'opened', 'clicked'];

        $notifications = [
            [
                'title' => 'Rappel de cycle',
                'message' => 'Votre période devrait commencer dans 3 jours',
                'category' => 'cycle',
                'icon' => '🩸',
            ],
            [
                'title' => 'Nouveau conseil santé',
                'message' => 'Découvrez nos conseils pour une alimentation équilibrée pendant votre cycle',
                'category' => 'health_tip',
                'icon' => '💡',
            ],
            [
                'title' => 'Nouvel article',
                'message' => 'Un nouvel article sur la santé reproductive est disponible',
                'category' => 'article',
                'icon' => '📚',
            ],
            [
                'title' => 'Quiz du jour',
                'message' => 'Testez vos connaissances sur la santé reproductive',
                'category' => 'quiz',
                'icon' => '❓',
            ],
            [
                'title' => 'Alerte confirmée',
                'message' => 'Votre signalement a été pris en compte par nos équipes',
                'category' => 'alert',
                'icon' => '✅',
            ],
            [
                'title' => 'Nouvelle vidéo',
                'message' => 'Une nouvelle vidéo éducative est disponible',
                'category' => 'video',
                'icon' => '🎥',
            ],
            [
                'title' => 'Conseil du jour',
                'message' => 'Prenez soin de votre santé mentale et physique',
                'category' => 'health_tip',
                'icon' => '🩺',
            ],
            [
                'title' => 'Suivi de grossesse',
                'message' => 'N\'oubliez pas votre consultation prénatale cette semaine',
                'category' => 'reminder',
                'icon' => '🤰',
            ],
            [
                'title' => 'Centre de santé à proximité',
                'message' => 'Un nouveau centre de santé a été ajouté près de chez vous',
                'category' => 'general',
                'icon' => '🏥',
            ],
            [
                'title' => 'Message important',
                'message' => 'Vous avez reçu un nouveau message dans le forum',
                'category' => 'general',
                'icon' => '💬',
            ],
            [
                'title' => 'Période terminée',
                'message' => 'Votre période devrait se terminer aujourd\'hui',
                'category' => 'cycle',
                'icon' => '🩸',
            ],
            [
                'title' => 'Ovulation prévue',
                'message' => 'Votre période d\'ovulation commence demain',
                'category' => 'cycle',
                'icon' => '🔔',
            ],
            [
                'title' => 'Conseil nutrition',
                'message' => 'Pensez à bien vous hydrater pendant votre cycle',
                'category' => 'health_tip',
                'icon' => '💧',
            ],
            [
                'title' => 'Nouveau quiz disponible',
                'message' => 'Testez vos connaissances sur la contraception',
                'category' => 'quiz',
                'icon' => '❓',
            ],
            [
                'title' => 'Article santé',
                'message' => 'Les bienfaits de l\'exercice pendant les menstruations',
                'category' => 'article',
                'icon' => '📖',
            ],
            [
                'title' => 'Rappel consultation',
                'message' => 'N\'oubliez pas votre rendez-vous gynécologique',
                'category' => 'reminder',
                'icon' => '📅',
            ],
            [
                'title' => 'Vidéo éducative',
                'message' => 'Comprendre le cycle menstruel en 5 minutes',
                'category' => 'video',
                'icon' => '🎬',
            ],
            [
                'title' => 'Alerte traitée',
                'message' => 'Votre signalement a été résolu',
                'category' => 'alert',
                'icon' => '✔️',
            ],
            [
                'title' => 'Conseil bien-être',
                'message' => 'Techniques de relaxation pour soulager les douleurs',
                'category' => 'health_tip',
                'icon' => '🧘',
            ],
            [
                'title' => 'Nouveau service',
                'message' => 'Service de téléconsultation maintenant disponible',
                'category' => 'general',
                'icon' => '📱',
            ],
            [
                'title' => 'Symptômes inhabituels',
                'message' => 'Vous avez signalé des symptômes inhabituels',
                'category' => 'alert',
                'icon' => '⚠️',
            ],
            [
                'title' => 'Article nutrition',
                'message' => 'Les aliments à privilégier pendant vos règles',
                'category' => 'article',
                'icon' => '🥗',
            ],
            [
                'title' => 'Quiz complété',
                'message' => 'Félicitations ! Vous avez terminé le quiz avec succès',
                'category' => 'quiz',
                'icon' => '🎉',
            ],
            [
                'title' => 'Rappel médicament',
                'message' => 'N\'oubliez pas de prendre votre contraception',
                'category' => 'reminder',
                'icon' => '💊',
            ],
            [
                'title' => 'Communauté',
                'message' => 'Une nouvelle discussion vous intéresse dans le forum',
                'category' => 'general',
                'icon' => '👥',
            ],
        ];

        foreach ($notifications as $index => $notifData) {
            $utilisateur = $utilisateurs->random();
            $status = $statuses[array_rand($statuses)];
            $type = $types[array_rand($types)];

            $sentAt = now()->subDays(rand(1, 30));
            $deliveredAt = $status !== 'sent' ? $sentAt->copy()->addSeconds(rand(1, 10)) : null;
            $openedAt = in_array($status, ['opened', 'clicked']) ? $deliveredAt->copy()->addMinutes(rand(1, 60)) : null;
            $clickedAt = $status === 'clicked' ? $openedAt->copy()->addSeconds(rand(5, 30)) : null;

            NotificationLog::create([
                'utilisateur_id' => $utilisateur->id,
                'notification_schedule_id' => null,
                'title' => $notifData['title'],
                'message' => $notifData['message'],
                'icon' => $notifData['icon'],
                'action' => null,
                'image' => null,
                'type' => $type,
                'category' => $notifData['category'],
                'status' => $status,
                'sent_at' => $sentAt,
                'delivered_at' => $deliveredAt,
                'opened_at' => $openedAt,
                'clicked_at' => $clickedAt,
                'failed_at' => null,
                'error_message' => null,
                'platform' => $utilisateur->platform ?? 'android',
                'fcm_message_id' => 'fcm_' . uniqid(),
            ]);
        }

        $this->command->info('25 notifications de log créées avec succès!');
    }
}
