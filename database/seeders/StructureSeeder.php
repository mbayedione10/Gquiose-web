<?php

namespace Database\Seeders;

use App\Models\Structure;
use App\Models\TypeStructure;
use App\Models\Ville;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    public function run(): void
    {
        $conakry = Ville::where('name', 'Conakry')->first();
        $kindia = Ville::where('name', 'Kindia')->first();
        $labe = Ville::where('name', 'Labé')->first();
        
        $hopitalNational = TypeStructure::where('name', 'Hôpital National')->first();
        $hopitalRegional = TypeStructure::where('name', 'Hôpital Régional')->first();
        $centreHealth = TypeStructure::where('name', 'Centre de Santé')->first();
        $pharmacie = TypeStructure::where('name', 'Pharmacie')->first();

        $structures = [
            [
                'name' => 'CHU Donka',
                'description' => 'Centre Hospitalier Universitaire de Donka - Principal hôpital de référence de Guinée',
                'latitude' => 9.5370,
                'longitude' => -13.6785,
                'phone' => '+224621111111',
                'type_structure_id' => $hopitalNational?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Donka, Conakry',
                'offre' => 'Services de médecine générale, chirurgie, pédiatrie, maternité, urgences 24h/24',
                'status' => true,
            ],
            [
                'name' => 'CHU Ignace Deen',
                'description' => 'Centre Hospitalier Universitaire Ignace Deen',
                'latitude' => 9.5092,
                'longitude' => -13.7122,
                'phone' => '+224621111112',
                'type_structure_id' => $hopitalNational?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Kaloum, Conakry',
                'offre' => 'Chirurgie spécialisée, traumatologie, orthopédie, soins intensifs',
                'status' => true,
            ],
            [
                'name' => 'Hôpital Sino-Guinéen',
                'description' => 'Hôpital de coopération sino-guinéenne',
                'latitude' => 9.6412,
                'longitude' => -13.5784,
                'phone' => '+224621111113',
                'type_structure_id' => $hopitalNational?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Kipé, Ratoma',
                'offre' => 'Médecine générale, chirurgie, imagerie médicale, laboratoire',
                'status' => true,
            ],
            [
                'name' => 'Hôpital Régional de Kindia',
                'description' => 'Hôpital régional de la préfecture de Kindia',
                'latitude' => 10.0570,
                'longitude' => -12.8470,
                'phone' => '+224621111114',
                'type_structure_id' => $hopitalRegional?->id,
                'ville_id' => $kindia?->id,
                'adresse' => 'Centre-ville, Kindia',
                'offre' => 'Médecine générale, pédiatrie, maternité, chirurgie de base',
                'status' => true,
            ],
            [
                'name' => 'Hôpital Régional de Labé',
                'description' => 'Hôpital régional de la préfecture de Labé',
                'latitude' => 11.3180,
                'longitude' => -12.2890,
                'phone' => '+224621111115',
                'type_structure_id' => $hopitalRegional?->id,
                'ville_id' => $labe?->id,
                'adresse' => 'Centre-ville, Labé',
                'offre' => 'Services de médecine générale, maternité, consultations externes',
                'status' => true,
            ],
            [
                'name' => 'Centre de Santé Matam',
                'description' => 'Centre de santé communautaire de Matam',
                'latitude' => 9.5350,
                'longitude' => -13.6500,
                'phone' => '+224621111116',
                'type_structure_id' => $centreHealth?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Matam, Conakry',
                'offre' => 'Consultations, vaccinations, soins de base, planning familial',
                'status' => true,
            ],
            [
                'name' => 'Centre de Santé Hamdallaye',
                'description' => 'Centre de santé de Hamdallaye',
                'latitude' => 9.6200,
                'longitude' => -13.5900,
                'phone' => '+224621111117',
                'type_structure_id' => $centreHealth?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Hamdallaye, Ratoma',
                'offre' => 'Consultations générales, pédiatrie, vaccinations',
                'status' => true,
            ],
            [
                'name' => 'Pharmacie Centrale',
                'description' => 'Pharmacie principale du centre-ville',
                'latitude' => 9.5140,
                'longitude' => -13.7120,
                'phone' => '+224621111118',
                'type_structure_id' => $pharmacie?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Avenue de la République, Kaloum',
                'offre' => 'Médicaments génériques et spécialisés, matériel médical',
                'status' => true,
            ],
            [
                'name' => 'Pharmacie Kipé',
                'description' => 'Pharmacie moderne de Kipé',
                'latitude' => 9.6380,
                'longitude' => -13.5800,
                'phone' => '+224621111119',
                'type_structure_id' => $pharmacie?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Kipé, Ratoma',
                'offre' => 'Vente de médicaments, conseils pharmaceutiques',
                'status' => true,
            ],
            [
                'name' => 'Pharmacie de Kindia',
                'description' => 'Pharmacie du centre de Kindia',
                'latitude' => 10.0600,
                'longitude' => -12.8500,
                'phone' => '+224621111120',
                'type_structure_id' => $pharmacie?->id,
                'ville_id' => $kindia?->id,
                'adresse' => 'Marché central, Kindia',
                'offre' => 'Médicaments essentiels, produits de parapharmacie',
                'status' => true,
            ],
        ];

        foreach ($structures as $structure) {
            Structure::firstOrCreate(
                ['phone' => $structure['phone']],
                $structure
            );
        }

        $this->command->info('✅ ' . count($structures) . ' structures sanitaires créées');
    }
}
