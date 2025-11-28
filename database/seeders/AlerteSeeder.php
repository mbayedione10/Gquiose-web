
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
        
        // Types de violences basées sur le genre (VBG)
        $violencePhysique = TypeAlerte::where('name', 'Violence Physique')->first();
        $violencePsychologique = TypeAlerte::where('name', 'Violence Psychologique')->first();
        $violenceSexuelle = TypeAlerte::where('name', 'Violence Sexuelle')->first();
        $violenceNumerique = TypeAlerte::where('name', 'Violence Numérique')->first();
        $harcelement = TypeAlerte::where('name', 'Harcèlement')->first();
        $violenceEconomique = TypeAlerte::where('name', 'Violence Économique')->first();

        $alertes = [
            [
                'ref' => 'ALRT-' . date('Y') . '-001',
                'description' => 'Victime de violences physiques répétées de la part de mon conjoint. Coups, gifles et menaces quotidiennes. J\'ai peur pour ma sécurité et celle de mes enfants.',
                'type_alerte_id' => $violencePhysique?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 9.5370,
                'longitude' => -13.6785,
                'date_incident' => now()->subDays(2),
                'heure_incident' => '22:30:00',
                'relation_agresseur' => 'conjoint',
                'impact' => json_encode(['Stress', 'Peur', 'Traumatisme', 'Blessures physiques']),
                'anonymat_souhaite' => true,
                'consentement_transmission' => true,
                'conseils_lus' => false,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-002',
                'description' => 'Mon ex-partenaire a diffusé mes photos intimes sur les réseaux sociaux sans mon consentement. Les images circulent sur WhatsApp et Facebook.',
                'type_alerte_id' => $violenceNumerique?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Confirmée',
                'latitude' => 9.5140,
                'longitude' => -13.7120,
                'plateformes' => json_encode(['Facebook', 'WhatsApp', 'Instagram']),
                'nature_contenu' => json_encode(['Photos intimes', 'Messages privés']),
                'urls_problematiques' => 'https://facebook.com/[profil-agresseur], https://wa.me/[numero]',
                'comptes_impliques' => '@agresseur123, Profil: Jean Dupont',
                'frequence_incidents' => 'continu',
                'date_incident' => now()->subDays(5),
                'heure_incident' => '14:20:00',
                'relation_agresseur' => 'ex_partenaire',
                'impact' => json_encode(['Humiliation', 'Dépression', 'Anxiété', 'Perte de confiance']),
                'anonymat_souhaite' => false,
                'consentement_transmission' => true,
                'conseils_lus' => true,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-003',
                'description' => 'Harcèlement sexuel sur mon lieu de travail. Mon supérieur hiérarchique me fait des avances déplacées et menace ma carrière si je refuse.',
                'type_alerte_id' => $harcelement?->id,
                'ville_id' => $kindia?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 10.0570,
                'longitude' => -12.8470,
                'date_incident' => now()->subWeeks(1),
                'heure_incident' => '16:00:00',
                'relation_agresseur' => 'collegue',
                'impact' => json_encode(['Stress', 'Peur', 'Baisse de performance', 'Insomnie']),
                'anonymat_souhaite' => true,
                'consentement_transmission' => true,
                'conseils_lus' => false,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-004',
                'description' => 'Mon mari contrôle tous mes revenus et m\'interdit de travailler. Je n\'ai aucun accès aux finances du ménage et dépends totalement de lui.',
                'type_alerte_id' => $violenceEconomique?->id,
                'ville_id' => $labe?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 11.3180,
                'longitude' => -12.2890,
                'date_incident' => now()->subMonths(1),
                'heure_incident' => '10:00:00',
                'relation_agresseur' => 'conjoint',
                'impact' => json_encode(['Dépendance financière', 'Isolement', 'Perte d\'autonomie']),
                'anonymat_souhaite' => false,
                'consentement_transmission' => true,
                'conseils_lus' => false,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-005',
                'description' => 'Cyber-harcèlement par messages sur Instagram et TikTok. Menaces de mort et insultes quotidiennes de la part d\'un ex-petit ami.',
                'type_alerte_id' => $violenceNumerique?->id,
                'ville_id' => $kankan?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 10.3850,
                'longitude' => -9.3060,
                'plateformes' => json_encode(['Instagram', 'TikTok', 'Snapchat']),
                'nature_contenu' => json_encode(['Messages', 'Commentaires', 'Messages directs']),
                'comptes_impliques' => '@harceleur_tiktok, @menaces_insta',
                'frequence_incidents' => 'quotidien',
                'date_incident' => now()->subDays(1),
                'heure_incident' => '23:45:00',
                'relation_agresseur' => 'ex_partenaire',
                'impact' => json_encode(['Peur', 'Anxiété', 'Insomnie', 'Stress post-traumatique']),
                'anonymat_souhaite' => true,
                'consentement_transmission' => true,
                'conseils_lus' => true,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-006',
                'description' => 'Violence psychologique et insultes constantes de mon partenaire. Il me rabaisse devant nos amis et ma famille.',
                'type_alerte_id' => $violencePsychologique?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Confirmée',
                'latitude' => 9.6412,
                'longitude' => -13.5784,
                'date_incident' => now()->subDays(3),
                'heure_incident' => '19:30:00',
                'relation_agresseur' => 'conjoint',
                'impact' => json_encode(['Dépression', 'Perte d\'estime de soi', 'Isolement social']),
                'anonymat_souhaite' => false,
                'consentement_transmission' => true,
                'conseils_lus' => true,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-007',
                'description' => 'Agression sexuelle lors d\'une soirée. L\'agresseur est une connaissance qui a profité de mon état de vulnérabilité.',
                'type_alerte_id' => $violenceSexuelle?->id,
                'ville_id' => $conakry?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 9.5350,
                'longitude' => -13.6500,
                'date_incident' => now()->subDays(7),
                'heure_incident' => '02:15:00',
                'relation_agresseur' => 'connaissance',
                'impact' => json_encode(['Traumatisme', 'Peur', 'Honte', 'Culpabilité', 'Stress post-traumatique']),
                'anonymat_souhaite' => true,
                'consentement_transmission' => true,
                'conseils_lus' => false,
            ],
            [
                'ref' => 'ALRT-' . date('Y') . '-008',
                'description' => 'Stalking et surveillance constante via applications de géolocalisation installées à mon insu sur mon téléphone par mon ex.',
                'type_alerte_id' => $violenceNumerique?->id,
                'ville_id' => $kindia?->id,
                'utilisateur_id' => $utilisateur?->id,
                'etat' => 'Non approuvée',
                'latitude' => 10.0600,
                'longitude' => -12.8500,
                'plateformes' => json_encode(['Applications de localisation', 'Logiciels espions']),
                'nature_contenu' => json_encode(['Géolocalisation', 'Surveillance téléphone']),
                'frequence_incidents' => 'continu',
                'date_incident' => now()->subDays(10),
                'heure_incident' => '00:00:00',
                'relation_agresseur' => 'ex_partenaire',
                'impact' => json_encode(['Peur', 'Paranoïa', 'Sentiment d\'insécurité', 'Violation de vie privée']),
                'anonymat_souhaite' => true,
                'consentement_transmission' => true,
                'conseils_lus' => false,
            ],
        ];

        foreach ($alertes as $alerte) {
            Alerte::firstOrCreate(
                ['ref' => $alerte['ref']],
                $alerte
            );
        }

        $this->command->info('✅ ' . count($alertes) . ' alertes VBG créées avec succès');
    }
}
