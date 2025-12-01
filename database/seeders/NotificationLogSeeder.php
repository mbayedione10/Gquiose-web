
<?php

namespace Database\Seeders;

use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class NotificationLogSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer des utilisatrices
        $utilisatrices = Utilisateur::where('sexe', 'F')->limit(5)->get();

        if ($utilisatrices->isEmpty()) {
            $this->command->warn('Aucune utilisatrice trouvée. Créez d\'abord des utilisatrices.');
            return;
        }

        // Récupérer les templates
        $templates = NotificationTemplate::all();

        if ($templates->isEmpty()) {
            $this->command->warn('Aucun template de notification trouvé.');
            return;
        }

        $notifications = [];

        // Notification 1: Nouvel article publié
        $template1 = $templates->where('name', 'Nouvel article publié')->first();
        if ($template1) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template1->title,
                'message' => str_replace('{{sujet}}', 'la santé maternelle', $template1->message),
                'icon' => $template1->icon,
                'action' => $template1->action,
                'type' => 'automatic',
                'category' => $template1->category,
                'status' => 'sent',
                'sent_at' => now()->subDays(2),
                'delivered_at' => now()->subDays(2)->addMinutes(1),
                'platform' => 'android',
            ];
        }

        // Notification 2: Alerte sanitaire
        $template2 = $templates->where('name', 'Nouvelle alerte sanitaire')->first();
        if ($template2) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template2->title,
                'message' => str_replace('{{message_alerte}}', 'Épidémie de paludisme dans votre zone. Prenez vos précautions.', $template2->message),
                'icon' => $template2->icon,
                'action' => $template2->action,
                'type' => 'triggered',
                'category' => $template2->category,
                'status' => 'delivered',
                'sent_at' => now()->subDays(5),
                'delivered_at' => now()->subDays(5)->addMinutes(2),
                'opened_at' => now()->subDays(5)->addHours(1),
                'platform' => 'android',
            ];
        }

        // Notification 3: Rappel vaccination
        $template3 = $templates->where('name', 'Rappel vaccination')->first();
        if ($template3) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template3->title,
                'message' => str_replace('{{date}}', '31 décembre 2025', $template3->message),
                'icon' => $template3->icon,
                'action' => $template3->action,
                'type' => 'automatic',
                'category' => $template3->category,
                'status' => 'clicked',
                'sent_at' => now()->subDays(1),
                'delivered_at' => now()->subDays(1)->addMinutes(1),
                'opened_at' => now()->subDays(1)->addHours(2),
                'clicked_at' => now()->subDays(1)->addHours(2)->addMinutes(5),
                'platform' => 'ios',
            ];
        }

        // Notification 4: Conseil santé quotidien
        $template4 = $templates->where('name', 'Conseil santé quotidien')->first();
        if ($template4) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template4->title,
                'message' => str_replace('{{conseil}}', 'Buvez au moins 1,5 litre d\'eau par jour pour rester hydratée.', $template4->message),
                'icon' => $template4->icon,
                'action' => $template4->action,
                'type' => 'automatic',
                'category' => $template4->category,
                'status' => 'sent',
                'sent_at' => now()->subHours(6),
                'delivered_at' => now()->subHours(6)->addMinutes(1),
                'platform' => 'android',
            ];
        }

        // Notification 5: Réponse au forum
        $template5 = $templates->where('name', 'Réponse au forum')->first();
        if ($template5) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template5->title,
                'message' => str_replace('{{auteur}}', 'Dr. Diallo', $template5->message),
                'icon' => $template5->icon,
                'action' => $template5->action,
                'type' => 'triggered',
                'category' => $template5->category,
                'status' => 'opened',
                'sent_at' => now()->subHours(12),
                'delivered_at' => now()->subHours(12)->addMinutes(1),
                'opened_at' => now()->subHours(11),
                'platform' => 'android',
            ];
        }

        // Notification 6: Nouvelle vidéo
        $template6 = $templates->where('name', 'Nouvelle vidéo')->first();
        if ($template6) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template6->title,
                'message' => str_replace('{{sujet}}', 'l\'allaitement maternel', $template6->message),
                'icon' => $template6->icon,
                'action' => $template6->action,
                'type' => 'automatic',
                'category' => $template6->category,
                'status' => 'sent',
                'sent_at' => now()->subDays(3),
                'delivered_at' => now()->subDays(3)->addMinutes(2),
                'platform' => 'ios',
            ];
        }

        // Notification 7: Demande d'évaluation
        $template7 = $templates->where('name', 'Demande d\'évaluation')->first();
        if ($template7) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template7->title,
                'message' => $template7->message,
                'icon' => $template7->icon,
                'action' => $template7->action,
                'type' => 'manual',
                'category' => $template7->category,
                'status' => 'delivered',
                'sent_at' => now()->subDays(7),
                'delivered_at' => now()->subDays(7)->addMinutes(1),
                'platform' => 'android',
            ];
        }

        // Notification 8: Dépistage gratuit
        $template8 = $templates->where('name', 'Dépistage gratuit')->first();
        if ($template8) {
            $message = str_replace('{{type}}', 'VIH', $template8->message);
            $message = str_replace('{{date_debut}}', '15 janvier', $message);
            $message = str_replace('{{date_fin}}', '20 janvier', $message);
            
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template8->title,
                'message' => $message,
                'icon' => $template8->icon,
                'action' => $template8->action,
                'type' => 'manual',
                'category' => $template8->category,
                'status' => 'clicked',
                'sent_at' => now()->subDays(4),
                'delivered_at' => now()->subDays(4)->addMinutes(1),
                'opened_at' => now()->subDays(4)->addHours(3),
                'clicked_at' => now()->subDays(4)->addHours(3)->addMinutes(10),
                'platform' => 'android',
            ];
        }

        // Notification 9: Conseil santé (variation)
        if ($template4) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template4->title,
                'message' => str_replace('{{conseil}}', 'Faites au moins 30 minutes d\'exercice physique par jour.', $template4->message),
                'icon' => $template4->icon,
                'action' => $template4->action,
                'type' => 'automatic',
                'category' => $template4->category,
                'status' => 'failed',
                'sent_at' => now()->subHours(2),
                'failed_at' => now()->subHours(2)->addMinutes(1),
                'error_message' => 'FCM token invalide',
                'platform' => 'android',
            ];
        }

        // Notification 10: Nouvel article (variation)
        if ($template1) {
            $notifications[] = [
                'utilisateur_id' => $utilisatrices->random()->id,
                'title' => $template1->title,
                'message' => str_replace('{{sujet}}', 'la nutrition infantile', $template1->message),
                'icon' => $template1->icon,
                'action' => $template1->action,
                'type' => 'automatic',
                'category' => $template1->category,
                'status' => 'pending',
                'platform' => 'ios',
            ];
        }

        // Créer les notifications
        foreach ($notifications as $notification) {
            NotificationLog::create($notification);
        }

        $this->command->info('✅ ' . count($notifications) . ' notifications créées avec succès');
    }
}
