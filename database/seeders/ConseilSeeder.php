<?php

namespace Database\Seeders;

use App\Models\Conseil;
use Illuminate\Database\Seeder;

class ConseilSeeder extends Seeder
{
    public function run(): void
    {
        $conseils = [
            'Lavez-vous les mains avec du savon avant chaque repas et après être allé aux toilettes.',
            'Dormez sous une moustiquaire imprégnée pour vous protéger du paludisme.',
            'Faites bouillir l\'eau pendant au moins 5 minutes avant de la boire.',
            'Consultez un médecin dès les premiers signes de fièvre.',
            'Allaitez votre bébé exclusivement au sein pendant les 6 premiers mois.',
            'Faites vacciner vos enfants selon le calendrier vaccinal.',
            'Mangez des fruits et légumes frais tous les jours.',
            'Pratiquez une activité physique régulière pour rester en bonne santé.',
            'Évitez l\'automédication et consultez toujours un professionnel de santé.',
            'Utilisez des préservatifs pour vous protéger contre les IST/VIH.',
            'Effectuez au moins 4 consultations prénatales pendant la grossesse.',
            'Accouchez dans une structure de santé avec du personnel qualifié.',
            'Éliminez les eaux stagnantes autour de votre maison pour éviter les moustiques.',
            'Conservez les aliments dans un endroit propre et frais.',
            'En cas de diarrhée chez un enfant, donnez-lui des SRO (sels de réhydratation orale).',
            'Espacez les naissances d\'au moins 3 ans pour la santé de la mère et de l\'enfant.',
            'Couvrez-vous la bouche avec un mouchoir ou le coude quand vous toussez.',
            'Ne partagez jamais vos médicaments avec d\'autres personnes.',
            'Faites dépister régulièrement votre tension artérielle et votre glycémie.',
            'Évitez la consommation excessive de sel, sucre et matières grasses.',
        ];

        foreach ($conseils as $message) {
            Conseil::firstOrCreate(['message' => $message]);
        }

        $this->command->info('✅ ' . count($conseils) . ' conseils santé créés');
    }
}
