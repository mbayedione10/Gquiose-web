<?php

namespace Database\Seeders;

use App\Models\TypeAlerte;
use Illuminate\Database\Seeder;

class TypeAlerteSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Types d'alertes VBG traditionnels
            ['name' => 'Violence Conjugale', 'status' => true],
            ['name' => 'Harcèlement Sexuel', 'status' => true],
            ['name' => 'Agression Sexuelle', 'status' => true],
            ['name' => 'Mariage Forcé', 'status' => true],
            ['name' => 'MGF (Excision)', 'status' => true],
            ['name' => 'Violence Scolaire', 'status' => true],
            ['name' => 'Exploitation Sexuelle', 'status' => true],

            // Violences Facilitées par les Technologies (VFT)
            ['name' => 'Cyberharcèlement', 'status' => true],
            ['name' => 'Harcèlement par Messagerie (SMS/Appels)', 'status' => true],
            ['name' => 'Diffusion Images Intimes (Revenge Porn)', 'status' => true],
            ['name' => 'Chantage / Extorsion en Ligne', 'status' => true],
            ['name' => 'Cyberstalking / Surveillance Numérique', 'status' => true],
            ['name' => 'Usurpation d\'Identité en Ligne', 'status' => true],
            ['name' => 'Création de Faux Profils pour Harceler', 'status' => true],
            ['name' => 'Hacking / Violation Vie Privée', 'status' => true],
            ['name' => 'Menaces en Ligne', 'status' => true],
            ['name' => 'Deepfake / Manipulation Média', 'status' => true],
            ['name' => 'Arnaque Sentimentale en Ligne (Romance Scam)', 'status' => true],
            ['name' => 'Exploitation Sexuelle via Internet', 'status' => true],

            // Autres
            ['name' => 'Autres Violences', 'status' => true],
        ];

        foreach ($types as $type) {
            TypeAlerte::firstOrCreate(
                ['name' => $type['name']],
                ['status' => $type['status']]
            );
        }

        $this->command->info('✅ '.count($types).' types d\'alertes VBG créés');
    }
}
