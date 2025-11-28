<?php

namespace Database\Seeders;

use App\Models\Plateforme;
use Illuminate\Database\Seeder;

class PlateformeSeeder extends Seeder
{
    public function run(): void
    {
        $plateformes = [
            ['nom' => 'Facebook', 'description' => 'Réseau social Facebook'],
            ['nom' => 'WhatsApp', 'description' => 'Application de messagerie WhatsApp'],
            ['nom' => 'Instagram', 'description' => 'Réseau social Instagram'],
            ['nom' => 'TikTok', 'description' => 'Plateforme de vidéos TikTok'],
            ['nom' => 'Telegram', 'description' => 'Application de messagerie Telegram'],
            ['nom' => 'Snapchat', 'description' => 'Application de messagerie Snapchat'],
            ['nom' => 'Twitter/X', 'description' => 'Réseau social Twitter/X'],
            ['nom' => 'LinkedIn', 'description' => 'Réseau social professionnel LinkedIn'],
            ['nom' => 'Email', 'description' => 'Courrier électronique'],
            ['nom' => 'Site web/blog', 'description' => 'Site web ou blog'],
            ['nom' => 'Application de rencontre', 'description' => 'Applications de rencontre (Tinder, Badoo...)'],
            ['nom' => 'Jeu en ligne', 'description' => 'Jeux en ligne'],
            ['nom' => 'SMS', 'description' => 'Messages SMS'],
            ['nom' => 'Autre', 'description' => 'Autre plateforme'],
        ];

        foreach ($plateformes as $plateforme) {
            Plateforme::create($plateforme);
        }
    }
}
