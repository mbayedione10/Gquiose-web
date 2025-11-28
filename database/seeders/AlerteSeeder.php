<?php

namespace Database\Seeders;

use App\Models\Alerte;
use App\Models\TypeAlerte;
use App\Models\Ville;
use App\Models\Utilisateur;
use App\Models\SousTypeViolenceNumerique;
use Illuminate\Database\Seeder;

class AlerteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données nécessaires
        $utilisateurs = Utilisateur::withoutGlobalScopes()->get();
        
        if ($utilisateurs->isEmpty()) {
            $this->command->warn('Assurez-vous que les seeders Utilisateur et Ville ont été exécutés en premier.');
            return;
        }
        
        $conakry = Ville::where('name', 'Conakry')->first();
        $kindia = Ville::where('name', 'Kindia')->first();
        $kankan = Ville::where('name', 'Kankan')->first();

        // Types d'alertes
        $violenceNumerique = TypeAlerte::where('name', 'Violence numérique')->first();
        $harcelementSexuel = TypeAlerte::where('name', 'Harcèlement sexuel')->first();
        $violencePhysique = TypeAlerte::where('name', 'Violence physique')->first();
        $mariageForce = TypeAlerte::where('name', 'Mariage forcé')->first();

        // Sous-types de violence numérique
        $sousTypes = SousTypeViolenceNumerique::all();

        if (!$conakry) {
            $this->command->warn('Assurez-vous que les seeders Ville ont été exécutés en premier.');
            return;
        }

        // Génération du numéro de suivi
        $numeroSuivi = 'VBG-' . date('Y') . '-' . str_pad(1, 6, '0', STR_PAD_LEFT);

        // Alerte 1 : Violence numérique - Revenge porn
        Alerte::create([
            'ref' => 'ALRT-' . date('Y') . '-001',
            'numero_suivi' => $numeroSuivi,
            'description' => 'Mon ex-partenaire a publié mes photos intimes sur les réseaux sociaux sans mon consentement. Les images sont partagées sur plusieurs groupes Facebook et WhatsApp.',
            'type_alerte_id' => $violenceNumerique?->id,
            'sous_type_violence_numerique_id' => $sousTypes->where('name', 'Revenge porn')->first()?->id,
            'ville_id' => $conakry?->id,
            'utilisateur_id' => $utilisateurs->random()->id,
            'etat' => 'Non approuvée',
            'latitude' => 9.6412,
            'longitude' => -13.5784,
            'preuves' => [
                ['path' => 'preuves/screenshot1.jpg', 'type' => 'image'],
                ['path' => 'preuves/screenshot2.jpg', 'type' => 'image'],
            ],
            'conseils_securite' => "1. Documentez tout : Prenez des captures d'écran de tous les contenus problématiques avant qu'ils ne soient supprimés.\n2. Signalez immédiatement : Utilisez les outils de signalement des plateformes (Facebook, WhatsApp) pour faire retirer les contenus.\n3. Contactez la police : Déposez une plainte formelle avec vos preuves.\n4. Protégez vos comptes : Changez tous vos mots de passe et activez l'authentification à deux facteurs.\n5. Bloquez l'agresseur : Sur toutes les plateformes et votre téléphone.\n6. Demandez du soutien : Contactez une association d'aide aux victimes.",
            'plateformes' => ['Facebook', 'WhatsApp', 'Instagram'],
            'nature_contenu' => ['Photos intimes', 'Messages privés'],
            'urls_problematiques' => 'https://facebook.com/[profil-agresseur], https://wa.me/[numero]',
            'comptes_impliques' => '@agresseur123, Profil: Jean Dupont',
            'frequence_incidents' => 'continu',
            'date_incident' => now()->subDays(5),
            'heure_incident' => '14:20:00',
            'relation_agresseur' => 'ex_partenaire',
            'impact' => ['Humiliation', 'Dépression', 'Anxiété', 'Perte de confiance'],
            'anonymat_souhaite' => false,
            'consentement_transmission' => true,
            'conseils_lus' => true,
        ]);

        // Alerte 2 : Harcèlement sexuel au travail
        Alerte::create([
            'ref' => 'ALRT-' . date('Y') . '-002',
            'numero_suivi' => 'VBG-' . date('Y') . '-' . str_pad(2, 6, '0', STR_PAD_LEFT),
            'description' => 'Harcèlement sexuel sur mon lieu de travail. Mon supérieur hiérarchique me fait des avances déplacées et menace ma carrière si je refuse.',
            'type_alerte_id' => $harcelementSexuel?->id,
            'ville_id' => $kindia?->id,
            'utilisateur_id' => $utilisateurs->random()->id,
            'etat' => 'Non approuvée',
            'latitude' => 10.0570,
            'longitude' => -12.8470,
            'date_incident' => now()->subWeeks(1),
            'heure_incident' => '16:00:00',
            'relation_agresseur' => 'collegue',
            'impact' => ['Stress', 'Anxiété', 'Peur de perdre son emploi'],
            'anonymat_souhaite' => true,
            'consentement_transmission' => false,
            'conseils_securite' => "1. Documentez chaque incident avec date, heure et témoins éventuels.\n2. Informez les ressources humaines par écrit.\n3. Conservez tous les emails, messages ou preuves.\n4. Ne restez jamais seule avec cette personne.\n5. Parlez-en à un délégué du personnel ou syndicat.\n6. Consultez un avocat spécialisé en droit du travail.",
            'conseils_lus' => false,
        ]);

        // Alerte 3 : Cyberharcèlement
        Alerte::create([
            'ref' => 'ALRT-' . date('Y') . '-003',
            'numero_suivi' => 'VBG-' . date('Y') . '-' . str_pad(3, 6, '0', STR_PAD_LEFT),
            'description' => 'Je reçois quotidiennement des messages d\'insultes et de menaces sur mes réseaux sociaux. L\'agresseur crée de faux comptes pour me harceler.',
            'type_alerte_id' => $violenceNumerique?->id,
            'sous_type_violence_numerique_id' => $sousTypes->where('name', 'Cyberharcèlement')->first()?->id,
            'ville_id' => $conakry?->id,
            'utilisateur_id' => $utilisateurs->random()->id,
            'etat' => 'Confirmée',
            'latitude' => 9.5370,
            'longitude' => -13.6785,
            'plateformes' => ['Instagram', 'TikTok', 'Messenger'],
            'nature_contenu' => ['Messages menaçants', 'Insultes', 'Commentaires diffamatoires'],
            'comptes_impliques' => '@fake_account1, @fake_account2, @harceleur_principal',
            'frequence_incidents' => 'quotidien',
            'date_incident' => now()->subDays(30),
            'heure_incident' => '22:30:00',
            'relation_agresseur' => 'inconnu',
            'impact' => ['Anxiété sévère', 'Troubles du sommeil', 'Isolement social'],
            'anonymat_souhaite' => false,
            'consentement_transmission' => true,
            'conseils_securite' => "1. Ne répondez jamais aux messages de harcèlement.\n2. Bloquez tous les comptes impliqués.\n3. Signalez chaque compte aux plateformes.\n4. Activez les paramètres de confidentialité au maximum.\n5. Conservez toutes les preuves (captures d'écran).\n6. Déposez plainte à la police avec vos preuves.\n7. Envisagez une pause des réseaux sociaux.",
            'conseils_lus' => true,
        ]);

        // Alerte 4 : Violence physique
        Alerte::create([
            'ref' => 'ALRT-' . date('Y') . '-004',
            'numero_suivi' => 'VBG-' . date('Y') . '-' . str_pad(4, 6, '0', STR_PAD_LEFT),
            'description' => 'Mon conjoint m\'a frappée hier soir lors d\'une dispute. Ce n\'est pas la première fois mais la violence s\'aggrave.',
            'type_alerte_id' => $violencePhysique?->id,
            'ville_id' => $kankan?->id,
            'utilisateur_id' => $utilisateurs->random()->id,
            'etat' => 'Non approuvée',
            'latitude' => 10.3852,
            'longitude' => -9.3064,
            'date_incident' => now()->subDay(),
            'heure_incident' => '21:45:00',
            'relation_agresseur' => 'conjoint',
            'impact' => ['Blessures physiques', 'Traumatisme', 'Peur', 'Honte'],
            'anonymat_souhaite' => true,
            'consentement_transmission' => true,
            'conseils_securite' => "DANGER IMMÉDIAT - AGISSEZ MAINTENANT :\n1. Si vous êtes en danger immédiat, appelez la police (117 ou 122).\n2. Rendez-vous dans un centre de santé pour faire constater vos blessures.\n3. Gardez le certificat médical en lieu sûr.\n4. Contactez un refuge pour femmes si vous ne pouvez rentrer chez vous.\n5. Préparez un sac d'urgence (papiers, argent, vêtements).\n6. Informez une personne de confiance de votre situation.\n7. Ne restez jamais seule avec votre agresseur.",
            'conseils_lus' => false,
        ]);

        // Alerte 5 : Mariage forcé
        Alerte::create([
            'ref' => 'ALRT-' . date('Y') . '-005',
            'numero_suivi' => 'VBG-' . date('Y') . '-' . str_pad(5, 6, '0', STR_PAD_LEFT),
            'description' => 'Ma famille veut me forcer à épouser un homme que je ne connais pas. J\'ai 17 ans et je veux continuer mes études.',
            'type_alerte_id' => $mariageForce?->id,
            'ville_id' => $conakry?->id,
            'utilisateur_id' => $utilisateurs->random()->id,
            'etat' => 'Non approuvée',
            'latitude' => 9.5092,
            'longitude' => -13.7122,
            'date_incident' => now()->subDays(3),
            'heure_incident' => '18:00:00',
            'relation_agresseur' => 'famille',
            'impact' => ['Stress intense', 'Peur', 'Sentiment d\'impuissance', 'Anxiété pour l\'avenir'],
            'anonymat_souhaite' => true,
            'consentement_transmission' => true,
            'conseils_securite' => "PROTECTION URGENTE NÉCESSAIRE :\n1. Contactez immédiatement une association de défense des droits des femmes.\n2. Parlez à un conseiller scolaire ou un enseignant de confiance.\n3. Le mariage des mineurs est illégal en Guinée.\n4. Vous avez le droit de refuser ce mariage.\n5. Contactez la brigade de protection des mineurs.\n6. Ne partez pas en voyage avec votre famille si vous suspectez un mariage.\n7. Gardez vos documents d'identité sur vous.\n8. Identifiez un lieu sûr où aller en cas d'urgence.",
            'conseils_lus' => false,
        ]);

        $this->command->info('5 alertes créées avec succès avec l\'architecture VBG complète.');
    }
}