<?php

namespace Database\Seeders;

use App\Models\CategorieConseil;
use App\Models\ItemConseil;
use App\Models\SectionConseil;
use App\Models\SousTypeViolenceNumerique;
use App\Models\TypeAlerte;
use Illuminate\Database\Seeder;

class ConseilsMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping des types d'alertes par nom partiel
        $typeAlerteMapping = [
            'violence conjugale' => 'Violence Conjugale',
            'harcèlement sexuel' => 'Harcèlement Sexuel',
            'agression sexuelle' => 'Agression Sexuelle',
            'mariage forcé' => 'Mariage Forcé',
            'mgf' => 'MGF',
            'cyberharcèlement' => 'Cyberharcèlement',
            'revenge porn' => 'Revenge Porn',
            'chantage' => 'Chantage en Ligne',
            'cyberstalking' => 'Cyberstalking',
            'usurpation' => 'Usurpation d\'Identité',
            'hacking' => 'Hacking',
            'menaces en ligne' => 'Menaces en Ligne',
            'deepfake' => 'Deepfake',
        ];

        // Mapping des sous-types de violence numérique
        $sousTypeMapping = [
            'Harcèlement sur réseaux sociaux',
            'Harcèlement par messagerie (SMS)',
            'Chantage avec photos/vidéos intimes (sextorsion)',
            'Menaces ou insultes répétées en ligne',
            'Partage non-consensuel d\'images intimes (revenge porn)',
            'Surveillance/espionnage via téléphone',
            'Usurpation d\'identité en ligne',
            'Arnaque sentimentale',
            'Exploitation sexuelle via internet',
            'Création de faux profils pour harceler',
            'Autre violence numérique',
        ];

        // 1. Catégorie par défaut (conseils généraux)
        $this->createGeneralAdvice();

        // 2. Catégories pour les types d'alertes traditionnels
        $this->createViolenceConjugaleAdvice();
        $this->createHarcelementSexuelAdvice();
        $this->createAgressionSexuelleAdvice();
        $this->createMariageForceAdvice();
        $this->createMGFAdvice();

        // 3. Catégories pour les types d'alertes numériques
        $this->createCyberharcelementAdvice();
        $this->createRevengePornAdvice();
        $this->createChantageEnLigneAdvice();
        $this->createCyberstalkingAdvice();
        $this->createUsurpationIdentiteAdvice();
        $this->createHackingAdvice();
        $this->createMenacesEnLigneAdvice();
        $this->createDeepfakeAdvice();

        // 4. Catégories pour les sous-types de violence numérique
        $this->createHarcelementReseauxSociauxAdvice();
        $this->createHarcelementSMSAdvice();
        $this->createArnaqueSentimentaleAdvice();
        $this->createExploitationSexuelleAdvice();
        $this->createFauxProfilsAdvice();
    }

    private function createGeneralAdvice(): void
    {
        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS DE SÉCURITÉ GÉNÉRAUX',
            'emoji' => '⚠️',
            'is_default' => true,
            'ordre' => 0,
            'status' => true,
        ]);

        $this->createSection($categorie, 'SÉCURITÉ IMMÉDIATE', '🔒', 1, [
            'Si tu es en danger immédiat, appelle la police (117) ou OPROGEM (116)',
            'Éloigne-toi de la situation dangereuse si possible',
            'Parle à une personne de confiance',
        ]);

        $this->createSection($categorie, 'SÉCURITÉ NUMÉRIQUE', '📱', 2, [
            'Ne supprime pas les preuves (messages, photos, emails)',
            'Fais des captures d\'écran de tout',
            'Sauvegarde les preuves dans un endroit sûr (cloud privé, clé USB cachée)',
        ]);

        $this->createSection($categorie, 'OBTENIR DE L\'AIDE', '🆘', 3, [
            'Centre d\'Écoute OPROGEM : 116 (gratuit, 24h/24)',
            'Centre Sabou Guinée : +224 621 000 006',
            'Guichet Unique VBG CHU Donka : +224 621 000 007',
            'Utilise l\'app GquiOse pour trouver un centre d\'aide près de toi',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 4, [
            'Tes informations sont confidentielles. Tu n\'es pas seul.e.',
        ]);
    }

    private function createViolenceConjugaleAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%violence conjugale%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS SPÉCIFIQUES - VIOLENCE CONJUGALE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 1,
            'status' => true,
        ]);

        $this->createSection($categorie, 'SÉCURITÉ IMMÉDIATE', '🆘', 1, [
            'Si tu es en danger maintenant : appelle la police (117) ou OPROGEM (116)',
            'Prépare un sac d\'urgence caché (papiers, argent, vêtements, médicaments)',
            'Identifie des lieux sûrs où aller (famille, amis, centre d\'accueil)',
        ]);

        $this->createSection($categorie, 'PREUVES ET DOCUMENTATION', '📝', 2, [
            'Prends des photos de tes blessures (avec dates)',
            'Conserve les messages menaçants ou violents',
            'Note les dates, heures et détails des incidents',
            'Consulte un médecin pour certificat médical',
        ]);

        $this->createSection($categorie, 'SÉCURITÉ NUMÉRIQUE', '🔒', 3, [
            'Change tes mots de passe depuis un appareil sûr',
            'Vérifie que ton téléphone n\'a pas d\'applications de surveillance',
            'Utilise le mode navigation privée pour chercher de l\'aide',
            'Efface l\'historique de navigation après',
        ]);

        $this->createSection($categorie, 'AIDE DISPONIBLE', '📞', 4, [
            'OPROGEM (116) - Écoute et orientation 24h/24',
            'Centre Sabou Guinée - Assistance juridique',
            'Association des Juristes Guinéennes - Aide légale gratuite',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 5, [
            'Tu mérites de vivre sans violence. Ce n\'est PAS de ta faute.',
        ]);
    }

    private function createHarcelementSexuelAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%harcèlement sexuel%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS SPÉCIFIQUES - HARCÈLEMENT SEXUEL',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 2,
            'status' => true,
        ]);

        $this->createSection($categorie, 'DIS NON CLAIREMENT', '🛑', 1, [
            'Dis fermement que ce comportement est inacceptable',
            'N\'aie pas peur de dire NON, même à un supérieur',
            'Tu n\'as RIEN fait pour provoquer ça',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📝', 2, [
            'Garde TOUS les messages, emails, notes',
            'Fais des captures d\'écran avec dates visibles',
            'Note : dates, lieux, témoins, ce qui a été dit/fait',
            'Conserve les preuves dans plusieurs endroits sûrs',
        ]);

        $this->createSection($categorie, 'PARLE-EN', '👥', 3, [
            'À une personne de confiance',
            'Au service RH (si travail) ou direction (si école)',
            'À un centre d\'écoute VBG',
        ]);

        $this->createSection($categorie, 'OPTIONS LÉGALES', '⚖️', 4, [
            'Tu peux porter plainte à la police',
            'Contacte l\'Association des Juristes Guinéennes pour aide juridique',
            'Le harcèlement sexuel est un DÉLIT en Guinée',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 5, [
            'OPROGEM : 116',
            'Centre Sabou : +224 621 000 006',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 6, [
            'Le harcèlement n\'est JAMAIS acceptable. Tu as le droit de dire NON.',
        ]);
    }

    private function createAgressionSexuelleAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%agression sexuelle%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS URGENTS - AGRESSION SEXUELLE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 3,
            'status' => true,
        ]);

        $this->createSection($categorie, 'DANS LES 72 HEURES', '🆘', 1, [
            'VA IMMÉDIATEMENT au Guichet Unique VBG (CHU Donka) ou CHU Ignace Deen',
            'C\'est GRATUIT et CONFIDENTIEL',
            'Traitement d\'urgence : prophylaxie IST/VIH, contraception d\'urgence',
            'Certificat médical pour plainte',
        ]);

        $this->createSection($categorie, 'PREUVES MÉDICALES', '⚠️', 2, [
            'Si possible, ne te lave pas, ne change pas de vêtements avant examen médical',
            'Conserve les vêtements dans un sac papier (pas plastique)',
            'Même si tu t\'es lavé.e, va quand même à l\'hôpital',
        ]);

        $this->createSection($categorie, 'PORTER PLAINTE', '📝', 3, [
            'Tu as le DROIT de porter plainte',
            'Le viol est un CRIME en Guinée',
            'L\'Association des Juristes Guinéennes peut t\'accompagner gratuitement',
            'Tu n\'es pas obligé.e de porter plainte, mais c\'est ton droit',
        ]);

        $this->createSection($categorie, 'SOUTIEN PSYCHOLOGIQUE', '🧠', 4, [
            'Centre d\'Écoute OPROGEM : 116 (24h/24)',
            'Centre Sabou : soutien psychologique gratuit',
            'Il est normal de ressentir peur, colère, honte - parle-en',
        ]);

        $this->createSection($categorie, 'URGENCES', '📞', 5, [
            'Guichet Unique VBG CHU Donka : +224 621 000 007',
            'OPROGEM : 116',
            'Police : 117',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 6, [
            'Ce n\'est PAS de ta faute. Tu n\'es pas seul.e. L\'aide existe.',
        ]);
    }

    private function createMariageForceAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%mariage forcé%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - MARIAGE FORCÉ',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 4,
            'status' => true,
        ]);

        $this->createSection($categorie, 'TES DROITS', '⚖️', 1, [
            'Le mariage forcé est ILLÉGAL en Guinée',
            'Tu as le DROIT de refuser',
            'L\'âge légal du mariage est 18 ans',
        ]);

        $this->createSection($categorie, 'SI TU ES EN DANGER', '🆘', 2, [
            'Contacte OPROGEM : 116 (24h/24)',
            'L\'Association des Juristes Guinéennes peut intervenir',
            'Possibilité d\'hébergement d\'urgence',
        ]);

        $this->createSection($categorie, 'AIDE JURIDIQUE GRATUITE', '📞', 3, [
            'Association des Juristes Guinéennes : +224 621 000 013',
            'Peuvent parler à ta famille en ton nom',
            'Peuvent saisir la justice si nécessaire',
        ]);

        $this->createSection($categorie, 'AGIS VITE', '💪', 4, [
            'Plus tu appelles tôt, plus on peut t\'aider',
            'Parle à un.e enseignant.e, imam, prêtre de confiance',
            'Tes études sont plus importantes qu\'un mariage précoce',
        ]);

        $this->createSection($categorie, 'PREUVES', '📱', 5, [
            'Enregistre les conversations (si sûr de le faire)',
            'Note dates et personnes impliquées',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 6, [
            'Tu as le droit de choisir ta vie. Le mariage forcé est un CRIME.',
        ]);
    }

    private function createMGFAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%mgf%')
            ->orWhere('name', 'like', '%excision%')
            ->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - MGF / EXCISION',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 5,
            'status' => true,
        ]);

        $this->createSection($categorie, 'L\'EXCISION EST ILLÉGALE', '⚖️', 1, [
            'Les MGF sont INTERDITES par la loi guinéenne',
            'C\'est une VIOLENCE, pas une tradition à respecter',
        ]);

        $this->createSection($categorie, 'SI TU ES MENACÉE D\'EXCISION', '🆘', 2, [
            'Appelle IMMÉDIATEMENT OPROGEM : 116',
            'Contacte Fraternité Médicale Guinée : +224 621 000 014',
            'La police DOIT te protéger',
        ]);

        $this->createSection($categorie, 'SI TU AS DÉJÀ ÉTÉ EXCISÉE', '🏥', 3, [
            'Consulte un médecin pour complications éventuelles',
            'Soutien psychologique disponible gratuitement',
            'Reconstruction chirurgicale possible (demande info)',
        ]);

        $this->createSection($categorie, 'PORTER PLAINTE', '⚖️', 4, [
            'Tu peux dénoncer les responsables',
            'Association des Juristes Guinéennes : aide juridique gratuite',
            'Même ta famille peut être poursuivie si impliquée',
        ]);

        $this->createSection($categorie, 'AIDE SPÉCIALISÉE', '📞', 5, [
            'Fraternité Médicale Guinée : +224 621 000 014',
            'OPROGEM : 116',
            'Centre Sabou Guinée : +224 621 000 006',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 6, [
            'Ton corps t\'appartient. L\'excision est une MUTILATION, pas une culture.',
        ]);
    }

    private function createCyberharcelementAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%cyberharcèlement%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - CYBERHARCÈLEMENT',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 6,
            'status' => true,
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI IMMÉDIATEMENT', '🛑', 1, [
            'BLOQUE la personne sur tous les réseaux sociaux',
            'Mets tes comptes en PRIVÉ temporairement',
            'Ne réponds PAS aux provocations',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📱', 2, [
            'CAPTURES D\'ÉCRAN de TOUT (messages, posts, commentaires)',
            'Inclus les dates, heures, noms d\'utilisateur',
            'Sauvegarde dans plusieurs endroits (email, cloud, clé USB)',
            'NE SUPPRIME RIEN avant d\'avoir sauvegardé',
        ]);

        $this->createSection($categorie, 'SÉCURITÉ DU COMPTE', '⚙️', 3, [
            'Change TOUS tes mots de passe',
            'Active l\'authentification à deux facteurs',
            'Vérifie les appareils connectés à tes comptes',
            'Révoque l\'accès aux applications suspectes',
        ]);

        $this->createSection($categorie, 'SIGNALE', '📢', 4, [
            'Signale le profil sur la plateforme (Facebook, Instagram, etc.)',
            'Contacte OPROGEM : 116',
            'Tu peux porter plainte à la police avec les captures d\'écran',
        ]);

        $this->createSection($categorie, 'PARLE-EN', '👥', 5, [
            'À un parent, ami.e, enseignant.e de confiance',
            'Ne reste pas seul.e face au harcèlement',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 6, [
            'Le cyberharcèlement est un DÉLIT. Tu n\'es pas responsable.',
        ]);
    }

    private function createRevengePornAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%revenge porn%')
            ->orWhere('name', 'like', '%images intimes%')
            ->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS URGENTS - DIFFUSION IMAGES INTIMES',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 7,
            'status' => true,
        ]);

        $this->createSection($categorie, 'ACTION IMMÉDIATE', '🚨', 1, [
            'C\'est un CRIME en Guinée (violation vie privée + chantage)',
            'VA PORTER PLAINTE dès maintenant',
            'Contacte OPROGEM : 116 pour accompagnement',
        ]);

        $this->createSection($categorie, 'RETRAIT DU CONTENU', '📱', 2, [
            'SIGNALE immédiatement sur la plateforme (Facebook, Instagram, WhatsApp)',
            'Demande le retrait d\'urgence (formulaire spécial pour contenu intime)',
            'Envoie email à : support@facebook.com, support@instagram.com',
            'Mentionne : \'non-consensual intimate images\' ou \'revenge porn\'',
        ]);

        $this->createSection($categorie, 'PREUVES', '📝', 3, [
            'Captures d\'écran AVANT que ça soit retiré',
            'URLs des posts/messages',
            'Profil de la personne qui a diffusé',
            'Conversations montrant le chantage/menaces',
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI', '🔒', 4, [
            'Mets TOUS tes comptes en PRIVÉ',
            'Change tes mots de passe',
            'Bloque la personne partout',
            'Vérifie que tes appareils n\'ont pas de spyware',
        ]);

        $this->createSection($categorie, 'ACTION LÉGALE', '⚖️', 5, [
            'Association des Juristes Guinéennes : aide juridique gratuite',
            'La personne risque la PRISON',
            'Tu peux demander des dommages-intérêts',
        ]);

        $this->createSection($categorie, 'SOUTIEN PSYCHOLOGIQUE', '🧠', 6, [
            'Centre Sabou : +224 621 000 006',
            'OPROGEM : 116',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'CE N\'EST PAS DE TA FAUTE. Envoyer des photos intimes ne justifie PAS leur diffusion.',
        ]);
    }

    private function createChantageEnLigneAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%chantage%')
            ->orWhere('name', 'like', '%extorsion%')
            ->first();

        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%sextorsion%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS URGENTS - CHANTAGE EN LIGNE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 8,
            'status' => true,
        ]);

        $this->createSection($categorie, 'NE CÈDE JAMAIS AU CHANTAGE', '🛑', 1, [
            'Ne paie JAMAIS (ça ne s\'arrête jamais)',
            'N\'envoie JAMAIS d\'autres photos/vidéos',
            'Ne fais RIEN de ce qu\'on te demande',
        ]);

        $this->createSection($categorie, 'COUPE LE CONTACT', '📱', 2, [
            'BLOQUE immédiatement la personne',
            'Ne réponds plus à AUCUN message',
            'Change tes mots de passe',
        ]);

        $this->createSection($categorie, 'PREUVES ESSENTIELLES', '📝', 3, [
            'CAPTURES D\'ÉCRAN de TOUTES les conversations de chantage',
            'Inclus les demandes d\'argent/photos/actions',
            'Note tous les comptes utilisés par le maître-chanteur',
            'Sauvegarde TOUT dans plusieurs endroits',
        ]);

        $this->createSection($categorie, 'VA À LA POLICE MAINTENANT', '🚨', 4, [
            'Le chantage est un CRIME grave',
            'La police peut tracer la personne',
            'Plus tu attends, plus c\'est difficile',
        ]);

        $this->createSection($categorie, 'AIDE JURIDIQUE', '⚖️', 5, [
            'Association des Juristes Guinéennes : +224 621 000 013',
            'OPROGEM : 116',
            'Centre Sabou : +224 621 000 006',
        ]);

        $this->createSection($categorie, 'SÉCURISE TES COMPTES', '🔒', 6, [
            'Active l\'authentification à 2 facteurs PARTOUT',
            'Vérifie les appareils connectés',
            'Change tous les mots de passe',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Le chantage NE S\'ARRÊTE que si tu portes plainte. N\'aie pas peur.',
        ]);
    }

    private function createCyberstalkingAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%cyberstalking%')
            ->orWhere('name', 'like', '%surveillance%')
            ->first();

        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%surveillance%')
            ->orWhere('nom', 'like', '%espionnage%')
            ->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - CYBERSTALKING / SURVEILLANCE NUMÉRIQUE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 9,
            'status' => true,
        ]);

        $this->createSection($categorie, 'DÉTECTE LA SURVEILLANCE', '🔍', 1, [
            'Quelqu\'un connaît tes mouvements sans que tu les aies partagés ?',
            'Tes publications privées sont connues ?',
            'Tu reçois des messages montrant qu\'on te surveille ?',
        ]);

        $this->createSection($categorie, 'VÉRIFIE TES APPAREILS', '📱', 2, [
            'Applications installées récemment (surtout cachées)',
            'Applications de surveillance : mSpy, FlexiSpy, Spyzie, etc.',
            'Partage de localisation activé (Google Maps, Find My, Life360)',
            'Accès iCloud/Google partagé avec quelqu\'un',
        ]);

        $this->createSection($categorie, 'REPRENDS LE CONTRÔLE', '🔒', 3, [
            'Change TOUS tes mots de passe depuis un appareil SÛR (pas le tien)',
            'Déconnecte TOUS les appareils de tes comptes',
            'Désactive le partage de localisation',
            'Révoque l\'accès aux applications tierces',
            'Réinitialise ton téléphone en mode usine (après sauvegarde)',
        ]);

        $this->createSection($categorie, 'SÉCURITÉ AVANCÉE', '⚙️', 4, [
            'Active l\'authentification à 2 facteurs PARTOUT',
            'Utilise un nouveau mot de passe UNIQUE pour chaque compte',
            'Vérifie les emails de connexion suspects',
            'Change de numéro SIM si nécessaire',
        ]);

        $this->createSection($categorie, 'DOCUMENTE TOUT', '📝', 5, [
            'Captures d\'écran des messages de stalking',
            'Liste des fois où la personne savait ta localisation',
            'Noms des apps suspectes trouvées',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 6, [
            'Le cyberstalking est un DÉLIT',
            'Police : 117 (avec preuves)',
            'OPROGEM : 116',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'La surveillance numérique est une forme de CONTRÔLE et de VIOLENCE.',
        ]);
    }

    private function createUsurpationIdentiteAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%usurpation%')->first();

        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%usurpation%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - USURPATION D\'IDENTITÉ EN LIGNE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 10,
            'status' => true,
        ]);

        $this->createSection($categorie, 'ACTION IMMÉDIATE', '🚨', 1, [
            'SIGNALE le faux profil sur la plateforme immédiatement',
            'Signale comme \'usurpation d\'identité\' ou \'fake account\'',
            'Facebook/Instagram ont des formulaires spéciaux pour ça',
        ]);

        $this->createSection($categorie, 'AVERTIS TON RÉSEAU', '📢', 2, [
            'Poste publiquement que ce n\'est PAS ton compte',
            'Préviens tes amis/famille de ne pas accepter ou interagir',
            'Demande-leur de signaler le faux profil aussi',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📝', 3, [
            'Captures d\'écran du faux profil (URL visible)',
            'Captures des fausses publications',
            'Messages reçus par la fausse identité',
            'Profils des personnes contactées par le faux compte',
        ]);

        $this->createSection($categorie, 'SÉCURISE TES COMPTES RÉELS', '🔒', 4, [
            'Change TOUS tes mots de passe',
            'Active l\'authentification à deux facteurs',
            'Vérifie les paramètres de confidentialité',
            'Limite qui peut voir tes photos',
        ]);

        $this->createSection($categorie, 'ACTION LÉGALE', '⚖️', 5, [
            'L\'usurpation d\'identité est un DÉLIT',
            'VA PORTER PLAINTE avec les captures d\'écran',
            'Association des Juristes Guinéennes : aide gratuite',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 6, [
            'OPROGEM : 116',
            'Police : 117',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Agis VITE : plus le faux profil reste actif, plus il peut nuire.',
        ]);
    }

    private function createHackingAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%hacking%')
            ->orWhere('name', 'like', '%violation%')
            ->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS URGENTS - HACKING / VIOLATION VIE PRIVÉE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 11,
            'status' => true,
        ]);

        $this->createSection($categorie, 'LIMITE LES DÉGÂTS', '🚨', 1, [
            'Change IMMÉDIATEMENT tes mots de passe depuis un appareil SÛR',
            'Déconnecte TOUS les appareils de tes comptes',
            'Active l\'authentification à 2 facteurs PARTOUT',
        ]);

        $this->createSection($categorie, 'VÉRIFIE TES COMPTES', '📧', 2, [
            'Email : vérifie les règles de transfert automatique',
            'Réseaux sociaux : vérifie les applications connectées',
            'Cloud (Google Drive, iCloud) : vérifie les partages',
            'Banque en ligne : vérifie les transactions',
        ]);

        $this->createSection($categorie, 'NETTOIE TES APPAREILS', '📱', 3, [
            'Scan antivirus complet',
            'Supprime les applications suspectes',
            'Réinitialise en mode usine si nécessaire',
        ]);

        $this->createSection($categorie, 'DOCUMENTE TOUT', '📝', 4, [
            'Captures d\'écran des activités suspectes',
            'Emails de connexion depuis lieux inconnus',
            'Messages/posts que tu n\'as pas envoyés',
            'Transactions bancaires non autorisées',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 5, [
            'Le hacking est un CRIME grave',
            'Police : 117 (apporte les preuves)',
            'Si argent volé : contacte ta banque immédiatement',
        ]);

        $this->createSection($categorie, 'SÉCURISE À LONG TERME', '🔐', 6, [
            'Utilise un gestionnaire de mots de passe',
            'Ne réutilise JAMAIS le même mot de passe',
            'Vérifie régulièrement les connexions actives',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 7, [
            'OPROGEM : 116',
            'Association des Juristes Guinéennes : +224 621 000 013',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 8, [
            'Agis VITE. Chaque minute compte pour limiter les dégâts.',
        ]);
    }

    private function createMenacesEnLigneAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%menaces en ligne%')->first();

        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%menaces%')
            ->orWhere('nom', 'like', '%insultes%')
            ->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - MENACES EN LIGNE',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 12,
            'status' => true,
        ]);

        $this->createSection($categorie, 'PRENDS-LE AU SÉRIEUX', '🚨', 1, [
            'TOUTE menace doit être prise au sérieux',
            'Même si tu penses que c\'est \'pour rire\'',
            'Les menaces en ligne peuvent devenir réelles',
        ]);

        $this->createSection($categorie, 'PREUVES CRUCIALES', '📝', 2, [
            'CAPTURES D\'ÉCRAN de TOUTES les menaces',
            'Inclus dates, heures, nom d\'utilisateur',
            'Sauvegarde dans plusieurs endroits',
            'NE SUPPRIME RIEN',
        ]);

        $this->createSection($categorie, 'NE RÉPONDS PAS', '🛑', 3, [
            'Ne réponds JAMAIS aux menaces',
            'Ça peut aggraver la situation',
            'Bloque la personne APRÈS avoir fait les captures',
        ]);

        $this->createSection($categorie, 'VA À LA POLICE MAINTENANT', '🚨', 4, [
            'Les menaces sont un DÉLIT',
            'La police peut intervenir AVANT qu\'il se passe quelque chose',
            'Apporte les captures d\'écran',
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI', '🔒', 5, [
            'Mets tes comptes en PRIVÉ',
            'Ne partage plus ta localisation publiquement',
            'Change tes habitudes si menaces physiques',
            'Informe ton entourage (famille, école, travail)',
        ]);

        $this->createSection($categorie, 'AIDE D\'URGENCE', '📞', 6, [
            'Police : 117',
            'OPROGEM : 116 (24h/24)',
            'Si danger immédiat : appelle la police directement',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Ne minimise JAMAIS une menace. Mieux vaut alerter pour rien que de ne rien faire.',
        ]);
    }

    private function createDeepfakeAdvice(): void
    {
        $typeAlerte = TypeAlerte::where('name', 'like', '%deepfake%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - DEEPFAKE / MANIPULATION MÉDIA',
            'emoji' => '⚠️',
            'type_alerte_id' => $typeAlerte?->id,
            'ordre' => 13,
            'status' => true,
        ]);

        $this->createSection($categorie, 'C\'EST TRÈS GRAVE', '🚨', 1, [
            'Les deepfakes sexuels sont un CRIME',
            'Manipulation d\'image pour nuire = violation vie privée',
            'Porte plainte IMMÉDIATEMENT',
        ]);

        $this->createSection($categorie, 'PREUVES ESSENTIELLES', '📝', 2, [
            'Captures d\'écran du contenu manipulé (URL visible)',
            'Captures des lieux où c\'est partagé',
            'Télécharge le contenu si possible (comme preuve)',
            'Liste des personnes qui l\'ont vu/partagé',
        ]);

        $this->createSection($categorie, 'RETRAIT DU CONTENU', '📱', 3, [
            'SIGNALE immédiatement sur la plateforme',
            'Mentionne \'manipulated media\', \'deepfake\', \'fake pornography\'',
            'Demande retrait d\'urgence',
            'Contacte le support de la plateforme directement',
        ]);

        $this->createSection($categorie, 'DÉMENS PUBLIQUEMENT', '📢', 4, [
            'Poste un message clair que c\'est FAUX',
            'Explique que c\'est une manipulation',
            'Demande à ton réseau de ne pas partager',
        ]);

        $this->createSection($categorie, 'ACTION LÉGALE URGENTE', '⚖️', 5, [
            'VA PORTER PLAINTE immédiatement',
            'Association des Juristes Guinéennes : +224 621 000 013',
            'La personne risque la PRISON',
            'Tu peux demander des dommages-intérêts importants',
        ]);

        $this->createSection($categorie, 'SOUTIEN PSYCHOLOGIQUE', '🧠', 6, [
            'Centre Sabou : +224 621 000 006',
            'OPROGEM : 116',
            'C\'est très traumatisant, ne reste pas seul.e',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Les deepfakes sont une forme de VIOLENCE SEXUELLE. Ce n\'est PAS de ta faute.',
        ]);
    }

    private function createHarcelementReseauxSociauxAdvice(): void
    {
        $sousType = SousTypeViolenceNumerique::where('nom', 'Harcèlement sur réseaux sociaux')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - HARCÈLEMENT SUR RÉSEAUX SOCIAUX',
            'emoji' => '⚠️',
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 14,
            'status' => true,
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI IMMÉDIATEMENT', '🛑', 1, [
            'BLOQUE la personne sur TOUS les réseaux sociaux',
            'Mets tes comptes en PRIVÉ temporairement',
            'Limite qui peut te contacter et commenter',
            'Ne réponds PAS aux provocations',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📱', 2, [
            'CAPTURES D\'ÉCRAN de TOUT (messages, posts, commentaires)',
            'Inclus les dates, heures, noms d\'utilisateur visibles',
            'Sauvegarde dans plusieurs endroits (email, cloud, clé USB)',
            'NE SUPPRIME RIEN avant d\'avoir sauvegardé',
        ]);

        $this->createSection($categorie, 'SIGNALE SUR LA PLATEFORME', '📢', 3, [
            'Facebook : Menu (3 points) > Signaler > Harcèlement',
            'Instagram : ... > Signaler > C\'est du harcèlement ou intimidation',
            'TikTok : Partager > Signaler > Harcèlement',
            'Twitter/X : ... > Signaler le tweet > Comportement abusif',
        ]);

        $this->createSection($categorie, 'SÉCURITÉ DU COMPTE', '⚙️', 4, [
            'Change TOUS tes mots de passe',
            'Active l\'authentification à deux facteurs',
            'Vérifie les appareils connectés à tes comptes',
            'Révoque l\'accès aux applications tierces suspectes',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 5, [
            'Le harcèlement en ligne est un DÉLIT',
            'Police : 117 (apporte les captures d\'écran)',
            'OPROGEM : 116 pour accompagnement',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 6, [
            'OPROGEM : 116 (24h/24)',
            'Centre Sabou : +224 621 000 006',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Le harcèlement n\'est JAMAIS acceptable. Tu as le droit d\'être en sécurité en ligne.',
        ]);
    }

    private function createHarcelementSMSAdvice(): void
    {
        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%SMS%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - HARCÈLEMENT PAR MESSAGERIE (SMS)',
            'emoji' => '⚠️',
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 15,
            'status' => true,
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI', '🛑', 1, [
            'BLOQUE le numéro immédiatement',
            'Active le filtre anti-spam de ton opérateur',
            'Ne réponds JAMAIS aux messages',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📱', 2, [
            'CAPTURES D\'ÉCRAN de TOUS les SMS (avec numéro et date visibles)',
            'Note les heures et fréquence des messages',
            'Sauvegarde dans plusieurs endroits',
            'NE SUPPRIME RIEN',
        ]);

        $this->createSection($categorie, 'CONTACTE TON OPÉRATEUR', '📞', 3, [
            'Orange Guinée : 111',
            'MTN Guinée : 1000',
            'Cellcom : 122',
            'Demande le blocage du numéro et historique des appels',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 4, [
            'Le harcèlement par SMS est un DÉLIT',
            'Police : 117 (apporte les captures d\'écran)',
            'L\'opérateur peut fournir les logs d\'appels à la police',
        ]);

        $this->createSection($categorie, 'OPTIONS TECHNIQUES', '⚙️', 5, [
            'Change de numéro si nécessaire (opérateur peut aider)',
            'Utilise une app de blocage d\'appels (Truecaller, etc.)',
            'Ne partage ton nouveau numéro qu\'avec des personnes de confiance',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 6, [
            'OPROGEM : 116',
            'Police : 117',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 7, [
            'Personne n\'a le droit de te harceler. Protège-toi.',
        ]);
    }

    private function createArnaqueSentimentaleAdvice(): void
    {
        $sousType = SousTypeViolenceNumerique::where('nom', 'Arnaque sentimentale')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - ARNAQUE SENTIMENTALE',
            'emoji' => '⚠️',
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 16,
            'status' => true,
        ]);

        $this->createSection($categorie, 'SIGNES D\'ARNAQUE', '🚨', 1, [
            'Déclaration d\'amour très rapide',
            'Refuse de se rencontrer ou de faire un appel vidéo',
            'Demande d\'argent (urgence médicale, voyage, etc.)',
            'Photos qui semblent professionnelles ou trop parfaites',
            'Histoire personnelle qui semble trop dramatique',
        ]);

        $this->createSection($categorie, 'ARRÊTE IMMÉDIATEMENT', '🛑', 2, [
            'N\'envoie JAMAIS d\'argent',
            'Ne partage AUCUNE information bancaire',
            'Ne donne pas de photos intimes',
            'BLOQUE la personne sur toutes les plateformes',
        ]);

        $this->createSection($categorie, 'VÉRIFIE L\'IDENTITÉ', '🔍', 3, [
            'Recherche inversée d\'image Google (les arnaqueurs utilisent des photos volées)',
            'Vérifie les profils sociaux (souvent récents avec peu d\'amis)',
            'Demande un appel vidéo immédiat (les arnaqueurs refusent)',
        ]);

        $this->createSection($categorie, 'PREUVES', '📝', 4, [
            'Captures d\'écran de TOUTES les conversations',
            'Profil de la personne',
            'Demandes d\'argent ou informations bancaires',
            'Relevés bancaires si tu as déjà envoyé de l\'argent',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 5, [
            'L\'arnaque sentimentale est un CRIME',
            'Police : 117 (apporte toutes les preuves)',
            'Contacte ta banque si tu as envoyé de l\'argent',
        ]);

        $this->createSection($categorie, 'PRÉVENTION FUTURE', '💡', 6, [
            'Méfie-toi des rencontres qui progressent trop vite',
            'Ne partage jamais d\'informations financières en ligne',
            'Toujours vérifier l\'identité avant de faire confiance',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 7, [
            'OPROGEM : 116',
            'Police cybercriminalité : 117',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 8, [
            'L\'amour véritable ne demande pas d\'argent. Si c\'est trop beau pour être vrai, c\'est probablement une arnaque.',
        ]);
    }

    private function createExploitationSexuelleAdvice(): void
    {
        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%exploitation sexuelle%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS URGENTS - EXPLOITATION SEXUELLE VIA INTERNET',
            'emoji' => '⚠️',
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 17,
            'status' => true,
        ]);

        $this->createSection($categorie, 'C\'EST TRÈS GRAVE', '🚨', 1, [
            'L\'exploitation sexuelle est un CRIME grave',
            'Tu es une VICTIME, pas une criminelle',
            'L\'aide existe et est GRATUITE',
        ]);

        $this->createSection($categorie, 'AIDE IMMÉDIATE', '🆘', 2, [
            'OPROGEM : 116 (24h/24, confidentiel)',
            'Centre Sabou Guinée : +224 621 000 006',
            'Police : 117 (tu seras protégée, pas jugée)',
        ]);

        $this->createSection($categorie, 'SI TU VEUX PORTER PLAINTE', '📝', 3, [
            'Collecte TOUTES les preuves (messages, profils, photos)',
            'Note tous les détails (noms, lieux, dates)',
            'L\'Association des Juristes Guinéennes peut t\'accompagner gratuitement',
            'Tu peux porter plainte de manière anonyme au début',
        ]);

        $this->createSection($categorie, 'PROTÈGE-TOI', '🔒', 4, [
            'Change TOUS tes mots de passe',
            'Bloque les personnes impliquées',
            'Ne supprime AUCUNE preuve',
            'Mets tes comptes en privé',
        ]);

        $this->createSection($categorie, 'SOUTIEN MÉDICAL ET PSYCHOLOGIQUE', '🏥', 5, [
            'Guichet Unique VBG CHU Donka : +224 621 000 007 (gratuit)',
            'Soins médicaux gratuits si nécessaire',
            'Accompagnement psychologique',
            'Tout est CONFIDENTIEL',
        ]);

        $this->createSection($categorie, 'TES DROITS', '⚖️', 6, [
            'Les exploiteurs risquent de LOURDES peines de prison',
            'Tu peux demander des dommages-intérêts',
            'Tu seras protégée pendant la procédure',
            'Ton identité peut rester confidentielle',
        ]);

        $this->createSection($categorie, 'URGENCES', '📞', 7, [
            'OPROGEM : 116 (24h/24)',
            'Police : 117',
            'Centre Sabou : +224 621 000 006',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 8, [
            'Tu n\'es PAS seule. Ce n\'est PAS de ta faute. L\'aide existe.',
        ]);
    }

    private function createFauxProfilsAdvice(): void
    {
        $sousType = SousTypeViolenceNumerique::where('nom', 'like', '%faux profils%')->first();

        $categorie = CategorieConseil::create([
            'nom' => 'CONSEILS - CRÉATION DE FAUX PROFILS POUR HARCELER',
            'emoji' => '⚠️',
            'sous_type_violence_numerique_id' => $sousType?->id,
            'ordre' => 18,
            'status' => true,
        ]);

        $this->createSection($categorie, 'IDENTIFIE LES FAUX PROFILS', '🚨', 1, [
            'Profil récent avec peu d\'amis',
            'Utilise tes photos ou ton nom',
            'Contacts répétés de comptes différents',
            'Messages similaires de profils différents',
        ]);

        $this->createSection($categorie, 'SIGNALE IMMÉDIATEMENT', '📢', 2, [
            'Sur chaque plateforme : Signaler > Faux compte',
            'Facebook : Formulaire spécial pour usurpation d\'identité',
            'Instagram : Signaler > C\'est un faux compte',
            'Demande le retrait urgent du profil',
        ]);

        $this->createSection($categorie, 'PRÉVIENS TON RÉSEAU', '📱', 3, [
            'Poste publiquement que ces comptes sont FAUX',
            'Demande à tes amis de signaler aussi',
            'Ne pas accepter ou interagir avec ces profils',
            'Partage la liste des faux comptes identifiés',
        ]);

        $this->createSection($categorie, 'COLLECTE DES PREUVES', '📝', 4, [
            'Captures d\'écran de TOUS les faux profils (URL visible)',
            'Captures des messages reçus',
            'Liste de tous les comptes suspects',
            'Sauvegarde dans plusieurs endroits',
        ]);

        $this->createSection($categorie, 'PROTÈGE TES COMPTES RÉELS', '🔒', 5, [
            'Mets tes comptes en PRIVÉ temporairement',
            'Limite qui peut voir tes photos et infos',
            'Active l\'authentification à deux facteurs',
            'Ajoute un watermark sur tes photos publiques',
        ]);

        $this->createSection($categorie, 'PORTE PLAINTE', '🚨', 6, [
            'La création de faux profils pour harceler est un DÉLIT',
            'Police : 117 (apporte les captures d\'écran)',
            'OPROGEM : 116 pour accompagnement',
        ]);

        $this->createSection($categorie, 'ACTION LÉGALE', '⚖️', 7, [
            'Association des Juristes Guinéennes : +224 621 000 013',
            'Les harceleurs risquent des poursuites',
            'Tu peux demander des dommages-intérêts',
        ]);

        $this->createSection($categorie, 'AIDE', '📞', 8, [
            'OPROGEM : 116',
            'Police : 117',
        ]);

        $this->createSection($categorie, 'IMPORTANT', '⚠️', 9, [
            'Agis VITE pour faire retirer les faux profils avant qu\'ils ne causent plus de dégâts.',
        ]);
    }

    /**
     * Helper pour créer une section avec ses items
     */
    private function createSection(CategorieConseil $categorie, string $titre, string $emoji, int $ordre, array $items): void
    {
        $section = SectionConseil::create([
            'categorie_conseil_id' => $categorie->id,
            'titre' => $titre,
            'emoji' => $emoji,
            'ordre' => $ordre,
            'status' => true,
        ]);

        foreach ($items as $index => $contenu) {
            ItemConseil::create([
                'section_conseil_id' => $section->id,
                'contenu' => $contenu,
                'ordre' => $index,
                'status' => true,
            ]);
        }
    }
}
