<?php

namespace Database\Seeders;

use App\Models\SousTypeViolenceNumerique;
use Illuminate\Database\Seeder;

class SousTypeViolenceNumeriqueSeeder extends Seeder
{
    public function run(): void
    {
        $sousTypes = [
            [
                'nom' => 'Harcèlement sur réseaux sociaux',
                'description' => 'Harcèlement via Facebook, Instagram, TikTok, WhatsApp, etc.',
                'status' => true,
            ],
            [
                'nom' => 'Harcèlement par messagerie (SMS)',
                'description' => 'Harcèlement répété par messages texte ou appels',
                'status' => true,
            ],
            [
                'nom' => 'Chantage avec photos/vidéos intimes (sextorsion)',
                'description' => 'Menace de diffuser des contenus intimes pour obtenir quelque chose',
                'status' => true,
            ],
            [
                'nom' => 'Menaces ou insultes répétées en ligne',
                'description' => 'Messages menaçants ou insultants de manière continue',
                'status' => true,
            ],
            [
                'nom' => 'Partage non-consensuel d\'images intimes (revenge porn)',
                'description' => 'Diffusion de photos/vidéos intimes sans consentement',
                'status' => true,
            ],
            [
                'nom' => 'Surveillance/espionnage via téléphone',
                'description' => 'Localisation, messages, appels espionnés sans consentement',
                'status' => true,
            ],
            [
                'nom' => 'Usurpation d\'identité en ligne',
                'description' => 'Création de faux comptes avec l\'identité de la victime',
                'status' => true,
            ],
            [
                'nom' => 'Arnaque sentimentale',
                'description' => 'Manipulation affective en ligne pour escroquer',
                'status' => true,
            ],
            [
                'nom' => 'Exploitation sexuelle via internet',
                'description' => 'Sollicitation ou exploitation sexuelle en ligne',
                'status' => true,
            ],
            [
                'nom' => 'Création de faux profils pour harceler',
                'description' => 'Utilisation de faux comptes pour harceler la victime',
                'status' => true,
            ],
            [
                'nom' => 'Autre violence numérique',
                'description' => 'Autre forme de violence numérique non listée',
                'status' => true,
            ],
        ];

        foreach ($sousTypes as $sousType) {
            SousTypeViolenceNumerique::firstOrCreate(
                ['nom' => $sousType['nom']],
                [
                    'description' => $sousType['description'],
                    'status' => $sousType['status'],
                ]
            );
        }

        $this->command->info('✅ ' . count($sousTypes) . ' sous-types de violence numérique créés');
    }
}
