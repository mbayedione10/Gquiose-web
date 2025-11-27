<?php

namespace Database\Seeders;

use App\Models\Alerte;
use App\Models\TypeAlerte;
use App\Models\Ville;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class AlerteSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer un utilisateur existant
        $utilisateur = Utilisateur::first();
        
        $conakry = Ville::where('name', 'Conakry')->first();
        $kindia = Ville::where('name', 'Kindia')->first();
        $labe = Ville::where('name', 'Labé')->first();
        $kankan = Ville::where('name', 'Kankan')->first();
        
        $epidemie = TypeAlerte::where('name', 'Épidémie')->first();
        $urgence = TypeAlerte::where('name', 'Urgence Sanitaire')->first();
        $vaccination = TypeAlerte::where('name', 'Campagne de Vaccination')->first();
        $information = TypeAlerte::where('name', 'Information Sanitaire')->first();
        $prevention = TypeAlerte::where('name', 'Prévention')->first();

        $alertes = [
            [
                'ref' => 'ALRT-' . date('Y') . '-001',
                'description' => 'Campagne de vaccination contre la rougeole du 1er au 15 mars. Tous les enfants de 6 mois à 15 ans sont concernés. Rendez-vous dans le centre de santé le plus proche.',
                'type_alerte_id' => $vaccination?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 9.5370,
                'longitude' => -13.6785,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-002',
                'description' => 'Dépistage gratuit du diabète et de l\'hypertension dans tous les centres de santé de la commune jusqu\'au 30 mars. Profitez de cette opportunité.',
                'type_alerte_id' => $information?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 9.5140,
                'longitude' => -13.7120,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-003',
                'description' => 'Attention : Augmentation des cas de diarrhée dans la région. Buvez uniquement de l\'eau traitée, lavez-vous les mains régulièrement et consultez en cas de symptômes.',
                'type_alerte_id' => $prevention?->id,
                'ville_id' => $kindia?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 10.0570,
                'longitude' => -12.8470,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-004',
                'description' => 'Début de la saison des pluies : Protégez-vous contre le paludisme. Dormez sous moustiquaire, éliminez les eaux stagnantes, consultez rapidement en cas de fièvre.',
                'type_alerte_id' => $prevention?->id,
                'ville_id' => $labe?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 11.3180,
                'longitude' => -12.2890,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-005',
                'description' => 'Journée mondiale de lutte contre le SIDA le 1er décembre. Dépistage gratuit et anonyme dans tous les centres de santé.',
                'type_alerte_id' => $information?->id,
                'ville_id' => $kankan?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 10.3850,
                'longitude' => -9.3060,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-006',
                'description' => 'Consultations prénatales gratuites tout le mois de mars. Futures mamans, prenez soin de vous et de votre bébé.',
                'type_alerte_id' => $information?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 9.6412,
                'longitude' => -13.5784,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-007',
                'description' => 'Formation gratuite sur les gestes de premiers secours le samedi 15 mars au Centre de Santé de Matam. Inscription sur place.',
                'type_alerte_id' => $information?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 9.5350,
                'longitude' => -13.6500,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-008',
                'description' => 'Stock de médicaments antipaludéens disponibles dans tous les centres de santé. Traitement gratuit pour les enfants de moins de 5 ans.',
                'type_alerte_id' => $information?->id,
                'ville_id' => $kindia?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'actif',
                'latitude' => 10.0600,
                'longitude' => -12.8500,
            ],
        ];

        foreach ($alertes as $alerte) {
            Alerte::firstOrCreate(
                ['ref' => $alerte['ref']],
                $alerte
            );
        }

        $this->command->info('✅ ' . count($alertes) . ' alertes créées');
    }
}
