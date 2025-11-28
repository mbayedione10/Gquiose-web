
<?php

namespace Database\Seeders;

use App\Models\Plateforme;
use Illuminate\Database\Seeder;

class PlateformeSeeder extends Seeder
{
    public function run(): void
    {
        $plateformes = [
            [
                'nom' => 'Facebook',
                'description' => 'Réseau social le plus utilisé au monde',
                'signalement_url' => 'https://www.facebook.com/help/contact/274459462613911',
                'status' => true,
            ],
            [
                'nom' => 'Instagram',
                'description' => 'Plateforme de partage de photos et vidéos',
                'signalement_url' => 'https://help.instagram.com/192435014247952',
                'status' => true,
            ],
            [
                'nom' => 'WhatsApp',
                'description' => 'Application de messagerie instantanée',
                'signalement_url' => 'https://www.whatsapp.com/safety/report',
                'status' => true,
            ],
            [
                'nom' => 'Twitter/X',
                'description' => 'Plateforme de microblogging',
                'signalement_url' => 'https://help.twitter.com/en/safety-and-security/report-abusive-behavior',
                'status' => true,
            ],
            [
                'nom' => 'TikTok',
                'description' => 'Plateforme de partage de vidéos courtes',
                'signalement_url' => 'https://www.tiktok.com/safety/report-a-problem',
                'status' => true,
            ],
            [
                'nom' => 'Snapchat',
                'description' => 'Application de messagerie avec photos/vidéos éphémères',
                'signalement_url' => 'https://support.snapchat.com/en-US/i-need-help',
                'status' => true,
            ],
            [
                'nom' => 'YouTube',
                'description' => 'Plateforme de partage de vidéos',
                'signalement_url' => 'https://support.google.com/youtube/answer/2802027',
                'status' => true,
            ],
            [
                'nom' => 'Telegram',
                'description' => 'Application de messagerie sécurisée',
                'signalement_url' => 'https://telegram.org/faq#q-there-39s-illegal-content-on-telegram-how-do-i-take-it-down',
                'status' => true,
            ],
            [
                'nom' => 'LinkedIn',
                'description' => 'Réseau social professionnel',
                'signalement_url' => 'https://www.linkedin.com/help/linkedin/answer/a1342051',
                'status' => true,
            ],
            [
                'nom' => 'Discord',
                'description' => 'Plateforme de communication pour communautés',
                'signalement_url' => 'https://discord.com/safety/360044103651-reporting-abusive-behavior-to-discord',
                'status' => true,
            ],
            [
                'nom' => 'Signal',
                'description' => 'Application de messagerie chiffrée',
                'signalement_url' => 'https://support.signal.org/hc/en-us/articles/360007318911-Report-spam-or-abuse',
                'status' => true,
            ],
            [
                'nom' => 'Messenger',
                'description' => 'Application de messagerie de Facebook',
                'signalement_url' => 'https://www.facebook.com/help/messenger-app/1643046452575269',
                'status' => true,
            ],
            [
                'nom' => 'Reddit',
                'description' => 'Plateforme de discussion et partage de contenu',
                'signalement_url' => 'https://www.reddithelp.com/hc/en-us/articles/360058752951',
                'status' => true,
            ],
            [
                'nom' => 'Autre',
                'description' => 'Autre plateforme ou réseau social',
                'signalement_url' => null,
                'status' => true,
            ],
        ];

        foreach ($plateformes as $plateforme) {
            Plateforme::updateOrCreate(
                ['nom' => $plateforme['nom']],
                $plateforme
            );
        }
    }
}
