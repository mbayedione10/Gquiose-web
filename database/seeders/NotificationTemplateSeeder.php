<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Nouvel article publié',
                'description' => 'Notification envoyée lors de la publication d\'un nouvel article',
                'title' => 'Nouvel article disponible',
                'message' => 'Un nouvel article sur {{sujet}} vient d\'être publié. Consultez-le maintenant!',
                'icon' => '📰',
                'action' => 'articles.show',
                'category' => 'content',
            ],
            [
                'name' => 'Nouvelle alerte sanitaire',
                'description' => 'Notification pour une alerte sanitaire importante',
                'title' => 'Alerte sanitaire',
                'message' => '{{message_alerte}}',
                'icon' => '🚨',
                'action' => 'alertes.show',
                'category' => 'health_tips',
            ],
            [
                'name' => 'Rappel vaccination',
                'description' => 'Rappel pour un rendez-vous de vaccination',
                'title' => 'Rappel : Vaccination importante',
                'message' => 'N\'oubliez pas de faire vacciner votre enfant. Campagne en cours jusqu\'au {{date}}.',
                'icon' => '💉',
                'action' => 'vaccinations',
                'category' => 'health_tips',
            ],
            [
                'name' => 'Conseil santé quotidien',
                'description' => 'Conseil santé envoyé quotidiennement',
                'title' => 'Conseil santé du jour',
                'message' => '{{conseil}}',
                'icon' => '💡',
                'action' => 'conseils',
                'category' => 'health_tips',
            ],
            [
                'name' => 'Réponse au forum',
                'description' => 'Notification quand quelqu\'un répond à votre message',
                'title' => 'Nouvelle réponse',
                'message' => '{{auteur}} a répondu à votre message dans le forum.',
                'icon' => '💬',
                'action' => 'forum.show',
                'category' => 'forum',
            ],
            [
                'name' => 'Nouvelle vidéo',
                'description' => 'Notification pour une nouvelle vidéo éducative',
                'title' => 'Nouvelle vidéo éducative',
                'message' => 'Découvrez notre nouvelle vidéo sur {{sujet}}',
                'icon' => '🎥',
                'action' => 'videos.show',
                'category' => 'content',
            ],
            [
                'name' => 'Demande d\'évaluation',
                'description' => 'Demander à l\'utilisateur d\'évaluer l\'application',
                'title' => 'Votre avis compte',
                'message' => 'Prenez 2 minutes pour évaluer notre application et nous aider à l\'améliorer.',
                'icon' => '⭐',
                'action' => 'evaluation',
                'category' => 'other',
            ],
            [
                'name' => 'Dépistage gratuit',
                'description' => 'Information sur une campagne de dépistage',
                'title' => 'Dépistage gratuit',
                'message' => 'Dépistage gratuit de {{type}} du {{date_debut}} au {{date_fin}}. Profitez-en!',
                'icon' => '🏥',
                'action' => 'depistages',
                'category' => 'health_tips',
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }

        $this->command->info('✅ '.count($templates).' templates de notifications créés');
    }
}
