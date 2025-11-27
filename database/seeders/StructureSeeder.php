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
        $kankan = Ville::where('name', 'Kankan')->first();

        // Types de structures orientées SSR/VBG pour jeunes
        $centreSanteJeunes = TypeStructure::where('name', 'Centre de Santé pour Jeunes')->first();
        $planningFamilial = TypeStructure::where('name', 'Centre de Planning Familial')->first();
        $centreEcouteVBG = TypeStructure::where('name', 'Centre d\'Écoute VBG')->first();
        $pointServiceJeunes = TypeStructure::where('name', 'Point de Service Jeunes')->first();
        $maisonJeunes = TypeStructure::where('name', 'Maison des Jeunes')->first();
        $priseEnChargeVBG = TypeStructure::where('name', 'Centre de Prise en Charge VBG')->first();
        $cliniqueAmieJeunes = TypeStructure::where('name', 'Clinique Amie des Jeunes')->first();
        $conseilDepistage = TypeStructure::where('name', 'Centre de Conseil et Dépistage')->first();
        $associationVBG = TypeStructure::where('name', 'Association de Lutte contre VBG')->first();

        $structures = [
            // Centres de Santé pour Jeunes - Conakry
            [
                'name' => 'Centre de Santé pour Jeunes de Matoto',
                'description' => 'Espace confidentiel et gratuit pour les jeunes de 10-24 ans. Services SSR adaptés aux adolescent.e.s',
                'latitude' => 9.5450,
                'longitude' => -13.6520,
                'phone' => '+224621000001',
                'type_structure_id' => $centreSanteJeunes?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Matoto Centre, près du marché',
                'offre' => 'Consultations gratuites SSR, contraception, dépistage IST/VIH, suivi menstruel, éducation sexuelle, consultation psychologique',
                'status' => true,
            ],
            [
                'name' => 'Espace Ados Ratoma',
                'description' => 'Point d\'accueil jeunes avec services SSR et VBG. Accueil sans jugement, confidentialité garantie',
                'latitude' => 9.6200,
                'longitude' => -13.5900,
                'phone' => '+224621000002',
                'type_structure_id' => $centreSanteJeunes?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Hamdallaye, Ratoma',
                'offre' => 'Consultations médicales jeunes, planning familial, contraception d\'urgence, tests de grossesse, counseling, groupes de parole',
                'status' => true,
            ],

            // Centres de Planning Familial
            [
                'name' => 'Centre de Planning Familial de Kaloum',
                'description' => 'Services de contraception et santé reproductive pour tous, y compris les jeunes non-mariés',
                'latitude' => 9.5092,
                'longitude' => -13.7122,
                'phone' => '+224621000003',
                'type_structure_id' => $planningFamilial?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Boulevard du Commerce, Kaloum',
                'offre' => 'Contraception (pilule, implant, DIU, préservatifs gratuits), consultation pré/post-natale, IVG médicalisée, dépistage cancer col utérus',
                'status' => true,
            ],
            [
                'name' => 'Planning Familial Kipé',
                'description' => 'Accès gratuit à la contraception pour les jeunes. Confidentialité respectée, pas besoin d\'autorisation parentale',
                'latitude' => 9.6380,
                'longitude' => -13.5800,
                'phone' => '+224621000004',
                'type_structure_id' => $planningFamilial?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Kipé Dadia, Ratoma',
                'offre' => 'Toutes méthodes contraceptives gratuites, pilule du lendemain, suivi gynécologique, éducation SSR, dépistage IST',
                'status' => true,
            ],

            // Centres d\'Écoute VBG
            [
                'name' => 'Centre d\'Écoute OPROGEM',
                'description' => 'Écoute et orientation des victimes de VBG. Ligne d\'urgence 116 disponible 24h/24',
                'latitude' => 9.5350,
                'longitude' => -13.6500,
                'phone' => '+224621000005',
                'type_structure_id' => $centreEcouteVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Matam, près du pont',
                'offre' => 'Écoute confidentielle, soutien psychologique, orientation juridique, médiation familiale, hébergement d\'urgence si nécessaire',
                'status' => true,
            ],
            [
                'name' => 'Centre Sabou Guinée',
                'description' => 'ONG de lutte contre les VBG. Accompagnement gratuit et confidentiel des victimes',
                'latitude' => 9.5140,
                'longitude' => -13.7100,
                'phone' => '+224621000006',
                'type_structure_id' => $centreEcouteVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Almamya, Kaloum',
                'offre' => 'Écoute psychologique, assistance juridique gratuite, accompagnement dépôt plainte, suivi médical post-agression',
                'status' => true,
            ],

            // Centres de Prise en Charge VBG
            [
                'name' => 'Guichet Unique VBG - CHU Donka',
                'description' => 'Prise en charge médicale, psychologique et juridique des victimes de VBG sous un même toit',
                'latitude' => 9.5370,
                'longitude' => -13.6785,
                'phone' => '+224621000007',
                'type_structure_id' => $priseEnChargeVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'CHU Donka, bâtiment annexe',
                'offre' => 'Soins d\'urgence post-viol (72h), prophylaxie IST/VIH, contraception d\'urgence, certificat médical, soutien psychologique, orientation police/justice',
                'status' => true,
            ],
            [
                'name' => 'Cellule VBG Ignace Deen',
                'description' => 'Service hospitalier spécialisé dans la prise en charge des victimes de violences sexuelles',
                'latitude' => 9.5092,
                'longitude' => -13.7150,
                'phone' => '+224621000008',
                'type_structure_id' => $priseEnChargeVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'CHU Ignace Deen, urgences',
                'offre' => 'Soins post-agression 24h/24, kit PEP (prophylaxie post-exposition VIH), certificat médical légal, accompagnement psychologique',
                'status' => true,
            ],

            // Points de Service Jeunes
            [
                'name' => 'Point Ado Dixinn',
                'description' => 'Lieu d\'information et de services SSR pour jeunes. Ambiance conviviale, sans jugement',
                'latitude' => 9.5500,
                'longitude' => -13.6900,
                'phone' => '+224621000009',
                'type_structure_id' => $pointServiceJeunes?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Dixinn Port, près université',
                'offre' => 'Distribution gratuite préservatifs, documentation SSR/VBG, orientation vers structures santé, ateliers éducatifs hebdomadaires',
                'status' => true,
            ],
            [
                'name' => 'Espace Jeunes Landréah',
                'description' => 'Point d\'accueil et d\'information SSR pour adolescent.e.s de quartier',
                'latitude' => 9.5600,
                'longitude' => -13.6300,
                'phone' => '+224621000010',
                'type_structure_id' => $pointServiceJeunes?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Landréah, Dixinn',
                'offre' => 'Info SSR, préservatifs gratuits, test grossesse, orientation planning familial, causeries éducatives pairs-éducateurs',
                'status' => true,
            ],

            // Maisons des Jeunes
            [
                'name' => 'Maison des Jeunes de Ratoma',
                'description' => 'Espace multidisciplinaire pour jeunes avec volet santé SSR/VBG',
                'latitude' => 9.6100,
                'longitude' => -13.6000,
                'phone' => '+224621000011',
                'type_structure_id' => $maisonJeunes?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Koloma, Ratoma',
                'offre' => 'Activités culturelles/sportives, ateliers SSR, séances cinéma-débat sur VBG, permanence conseiller santé jeunes, bibliothèque SSR',
                'status' => true,
            ],

            // Centres Conseil et Dépistage
            [
                'name' => 'Centre de Dépistage Volontaire (CDV) Matam',
                'description' => 'Dépistage gratuit et confidentiel IST/VIH pour tous, y compris mineurs',
                'latitude' => 9.5320,
                'longitude' => -13.6480,
                'phone' => '+224621000012',
                'type_structure_id' => $conseilDepistage?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Matam Lido',
                'offre' => 'Test VIH rapide (15 min), dépistage IST (gonorrhée, chlamydia, syphilis), counseling pré/post-test, orientation traitement ARV si besoin',
                'status' => true,
            ],

            // Associations de Lutte contre VBG
            [
                'name' => 'Association des Juristes Guinéennes (AJG)',
                'description' => 'Assistance juridique gratuite pour victimes de VBG et mariages forcés',
                'latitude' => 9.5250,
                'longitude' => -13.7050,
                'phone' => '+224621000013',
                'type_structure_id' => $associationVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Boulbinet, Kaloum',
                'offre' => 'Consultation juridique gratuite, accompagnement judiciaire, médiation familiale, sensibilisation droits des femmes et filles',
                'status' => true,
            ],
            [
                'name' => 'Fraternité Médicale Guinée (FMG)',
                'description' => 'ONG médicale avec focus SSR jeunes et lutte contre MGF',
                'latitude' => 9.5400,
                'longitude' => -13.6600,
                'phone' => '+224621000014',
                'type_structure_id' => $associationVBG?->id,
                'ville_id' => $conakry?->id,
                'adresse' => 'Taouyah, Dixinn',
                'offre' => 'Consultations SSR gratuites jeunes, lutte contre excision, éducation pairs, cliniques mobiles quartiers',
                'status' => true,
            ],

            // Structures en régions
            [
                'name' => 'Centre Jeunes de Kindia',
                'description' => 'Services SSR adaptés aux jeunes de Kindia et environs',
                'latitude' => 10.0570,
                'longitude' => -12.8470,
                'phone' => '+224621000015',
                'type_structure_id' => $centreSanteJeunes?->id,
                'ville_id' => $kindia?->id,
                'adresse' => 'Centre-ville, Kindia',
                'offre' => 'Consultations SSR, contraception gratuite, dépistage IST/VIH, éducation sexuelle, soutien psychologique',
                'status' => true,
            ],
            [
                'name' => 'Point Ado Labé',
                'description' => 'Espace jeunes avec services SSR et sensibilisation VBG',
                'latitude' => 11.3180,
                'longitude' => -12.2890,
                'phone' => '+224621000016',
                'type_structure_id' => $pointServiceJeunes?->id,
                'ville_id' => $labe?->id,
                'adresse' => 'Quartier Thiangol, Labé',
                'offre' => 'Info-conseil SSR, préservatifs gratuits, orientation structures santé, causeries VBG, lutte mariages précoces',
                'status' => true,
            ],
            [
                'name' => 'Centre VBG Kankan',
                'description' => 'Prise en charge globale des victimes de VBG en Haute-Guinée',
                'latitude' => 10.3850,
                'longitude' => -9.3064,
                'phone' => '+224621000017',
                'type_structure_id' => $priseEnChargeVBG?->id,
                'ville_id' => $kankan?->id,
                'adresse' => 'Bordo, Kankan',
                'offre' => 'Écoute psychologique, soins médicaux post-viol, assistance juridique, hébergement temporaire, réinsertion socio-économique',
                'status' => true,
            ],
        ];

        foreach ($structures as $structure) {
            Structure::firstOrCreate(
                ['phone' => $structure['phone']],
                $structure
            );
        }

        $this->command->info('✅ ' . count($structures) . ' structures SSR/VBG pour jeunes créées');
    }
}
