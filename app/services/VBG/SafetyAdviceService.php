<?php

namespace App\Services\VBG;

use App\Models\TypeAlerte;

class SafetyAdviceService
{
    /**
     * Génère des conseils de sécurité automatiques basés sur le type de violence
     *
     * @param int|null $typeAlerteId
     * @param int|null $sousTypeId
     * @return string
     */
    public function generateSafetyAdvice(?int $typeAlerteId, ?int $sousTypeId = null): string
    {
        // Si un sous-type de violence numérique est fourni, l'utiliser en priorité
        if ($sousTypeId) {
            $sousType = \App\Models\SousTypeViolenceNumerique::find($sousTypeId);
            if ($sousType) {
                return $this->getAdviceForSousType($sousType->nom);
            }
        }

        if (!$typeAlerteId) {
            return $this->getGeneralAdvice();
        }

        $typeAlerte = TypeAlerte::find($typeAlerteId);

        if (!$typeAlerte) {
            return $this->getGeneralAdvice();
        }

        return match (true) {
            // Violences traditionnelles
            str_contains(strtolower($typeAlerte->name), 'violence conjugale') => $this->getViolenceConjugaleAdvice(),
            str_contains(strtolower($typeAlerte->name), 'harcèlement sexuel') => $this->getHarcelementSexuelAdvice(),
            str_contains(strtolower($typeAlerte->name), 'agression sexuelle') => $this->getAgressionSexuelleAdvice(),
            str_contains(strtolower($typeAlerte->name), 'mariage forcé') => $this->getMariageForceAdvice(),
            str_contains(strtolower($typeAlerte->name), 'mgf') || str_contains(strtolower($typeAlerte->name), 'excision') => $this->getMGFAdvice(),

            // Violences facilitées par les technologies
            str_contains(strtolower($typeAlerte->name), 'cyberharcèlement') => $this->getCyberharcelementAdvice(),
            str_contains(strtolower($typeAlerte->name), 'revenge porn') || str_contains(strtolower($typeAlerte->name), 'images intimes') => $this->getRevengePornAdvice(),
            str_contains(strtolower($typeAlerte->name), 'chantage') || str_contains(strtolower($typeAlerte->name), 'extorsion') => $this->getChantageEnLigneAdvice(),
            str_contains(strtolower($typeAlerte->name), 'cyberstalking') || str_contains(strtolower($typeAlerte->name), 'surveillance') => $this->getCyberstalkingAdvice(),
            str_contains(strtolower($typeAlerte->name), 'usurpation') => $this->getUsurpationIdentiteAdvice(),
            str_contains(strtolower($typeAlerte->name), 'hacking') || str_contains(strtolower($typeAlerte->name), 'violation') => $this->getHackingAdvice(),
            str_contains(strtolower($typeAlerte->name), 'menaces en ligne') => $this->getMenacesEnLigneAdvice(),
            str_contains(strtolower($typeAlerte->name), 'deepfake') => $this->getDeepfakeAdvice(),

            default => $this->getGeneralAdvice(),
        };
    }

    private function getGeneralAdvice(): string
    {
        return "⚠️ CONSEILS DE SÉCURITÉ GÉNÉRAUX :\n\n" .
            "🔒 SÉCURITÉ IMMÉDIATE :\n" .
            "• Si tu es en danger immédiat, appelle la police (117) ou OPROGEM (116)\n" .
            "• Éloigne-toi de la situation dangereuse si possible\n" .
            "• Parle à une personne de confiance\n\n" .
            "📱 SÉCURITÉ NUMÉRIQUE :\n" .
            "• Ne supprime pas les preuves (messages, photos, emails)\n" .
            "• Fais des captures d'écran de tout\n" .
            "• Sauvegarde les preuves dans un endroit sûr (cloud privé, clé USB cachée)\n\n" .
            "🆘 OBTENIR DE L'AIDE :\n" .
            "• Centre d'Écoute OPROGEM : 116 (gratuit, 24h/24)\n" .
            "• Centre Sabou Guinée : +224 621 000 006\n" .
            "• Guichet Unique VBG CHU Donka : +224 621 000 007\n" .
            "• Utilise l'app GquiOse pour trouver un centre d'aide près de toi\n\n" .
            "⚠️ IMPORTANT : Tes informations sont confidentielles. Tu n'es pas seul.e.";
    }

    private function getViolenceConjugaleAdvice(): string
    {
        return "⚠️ CONSEILS SPÉCIFIQUES - VIOLENCE CONJUGALE :\n\n" .
            "🆘 SÉCURITÉ IMMÉDIATE :\n" .
            "• Si tu es en danger maintenant : appelle la police (117) ou OPROGEM (116)\n" .
            "• Prépare un sac d'urgence caché (papiers, argent, vêtements, médicaments)\n" .
            "• Identifie des lieux sûrs où aller (famille, amis, centre d'accueil)\n\n" .
            "📝 PREUVES ET DOCUMENTATION :\n" .
            "• Prends des photos de tes blessures (avec dates)\n" .
            "• Conserve les messages menaçants ou violents\n" .
            "• Note les dates, heures et détails des incidents\n" .
            "• Consulte un médecin pour certificat médical\n\n" .
            "🔒 SÉCURITÉ NUMÉRIQUE :\n" .
            "• Change tes mots de passe depuis un appareil sûr\n" .
            "• Vérifie que ton téléphone n'a pas d'applications de surveillance\n" .
            "• Utilise le mode navigation privée pour chercher de l'aide\n" .
            "• Efface l'historique de navigation après\n\n" .
            "📞 AIDE DISPONIBLE :\n" .
            "• OPROGEM (116) - Écoute et orientation 24h/24\n" .
            "• Centre Sabou Guinée - Assistance juridique\n" .
            "• Association des Juristes Guinéennes - Aide légale gratuite\n\n" .
            "⚠️ Tu mérites de vivre sans violence. Ce n'est PAS de ta faute.";
    }

    private function getHarcelementSexuelAdvice(): string
    {
        return "⚠️ CONSEILS SPÉCIFIQUES - HARCÈLEMENT SEXUEL :\n\n" .
            "🛑 DIS NON CLAIREMENT :\n" .
            "• Dis fermement que ce comportement est inacceptable\n" .
            "• N'aie pas peur de dire NON, même à un supérieur\n" .
            "• Tu n'as RIEN fait pour provoquer ça\n\n" .
            "📝 COLLECTE DES PREUVES :\n" .
            "• Garde TOUS les messages, emails, notes\n" .
            "• Fais des captures d'écran avec dates visibles\n" .
            "• Note : dates, lieux, témoins, ce qui a été dit/fait\n" .
            "• Conserve les preuves dans plusieurs endroits sûrs\n\n" .
            "👥 PARLE-EN :\n" .
            "• À une personne de confiance\n" .
            "• Au service RH (si travail) ou direction (si école)\n" .
            "• À un centre d'écoute VBG\n\n" .
            "⚖️ OPTIONS LÉGALES :\n" .
            "• Tu peux porter plainte à la police\n" .
            "• Contacte l'Association des Juristes Guinéennes pour aide juridique\n" .
            "• Le harcèlement sexuel est un DÉLIT en Guinée\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Centre Sabou : +224 621 000 006\n\n" .
            "⚠️ Le harcèlement n'est JAMAIS acceptable. Tu as le droit de dire NON.";
    }

    private function getAgressionSexuelleAdvice(): string
    {
        return "⚠️ CONSEILS URGENTS - AGRESSION SEXUELLE :\n\n" .
            "🆘 DANS LES 72 HEURES :\n" .
            "• VA IMMÉDIATEMENT au Guichet Unique VBG (CHU Donka) ou CHU Ignace Deen\n" .
            "• C'est GRATUIT et CONFIDENTIEL\n" .
            "• Traitement d'urgence : prophylaxie IST/VIH, contraception d'urgence\n" .
            "• Certificat médical pour plainte\n\n" .
            "⚠️ PREUVES MÉDICALES :\n" .
            "• Si possible, ne te lave pas, ne change pas de vêtements avant examen médical\n" .
            "• Conserve les vêtements dans un sac papier (pas plastique)\n" .
            "• Même si tu t'es lavé.e, va quand même à l'hôpital\n\n" .
            "📝 PORTER PLAINTE :\n" .
            "• Tu as le DROIT de porter plainte\n" .
            "• Le viol est un CRIME en Guinée\n" .
            "• L'Association des Juristes Guinéennes peut t'accompagner gratuitement\n" .
            "• Tu n'es pas obligé.e de porter plainte, mais c'est ton droit\n\n" .
            "🧠 SOUTIEN PSYCHOLOGIQUE :\n" .
            "• Centre d'Écoute OPROGEM : 116 (24h/24)\n" .
            "• Centre Sabou : soutien psychologique gratuit\n" .
            "• Il est normal de ressentir peur, colère, honte - parle-en\n\n" .
            "📞 URGENCES :\n" .
            "• Guichet Unique VBG CHU Donka : +224 621 000 007\n" .
            "• OPROGEM : 116\n" .
            "• Police : 117\n\n" .
            "⚠️ Ce n'est PAS de ta faute. Tu n'es pas seul.e. L'aide existe.";
    }

    private function getMariageForceAdvice(): string
    {
        return "⚠️ CONSEILS - MARIAGE FORCÉ :\n\n" .
            "⚖️ TES DROITS :\n" .
            "• Le mariage forcé est ILLÉGAL en Guinée\n" .
            "• Tu as le DROIT de refuser\n" .
            "• L'âge légal du mariage est 18 ans\n\n" .
            "🆘 SI TU ES EN DANGER :\n" .
            "• Contacte OPROGEM : 116 (24h/24)\n" .
            "• L'Association des Juristes Guinéennes peut intervenir\n" .
            "• Possibilité d'hébergement d'urgence\n\n" .
            "📞 AIDE JURIDIQUE GRATUITE :\n" .
            "• Association des Juristes Guinéennes : +224 621 000 013\n" .
            "• Peuvent parler à ta famille en ton nom\n" .
            "• Peuvent saisir la justice si nécessaire\n\n" .
            "💪 AGIS VITE :\n" .
            "• Plus tu appelles tôt, plus on peut t'aider\n" .
            "• Parle à un.e enseignant.e, imam, prêtre de confiance\n" .
            "• Tes études sont plus importantes qu'un mariage précoce\n\n" .
            "📱 PREUVES :\n" .
            "• Enregistre les conversations (si sûr de le faire)\n" .
            "• Note dates et personnes impliquées\n\n" .
            "⚠️ Tu as le droit de choisir ta vie. Le mariage forcé est un CRIME.";
    }

    private function getMGFAdvice(): string
    {
        return "⚠️ CONSEILS - MGF / EXCISION :\n\n" .
            "⚖️ IMPORTANTE : L'EXCISION EST ILLÉGALE :\n" .
            "• Les MGF sont INTERDITES par la loi guinéenne\n" .
            "• C'est une VIOLENCE, pas une tradition à respecter\n\n" .
            "🆘 SI TU ES MENACÉE D'EXCISION :\n" .
            "• Appelle IMMÉDIATEMENT OPROGEM : 116\n" .
            "• Contacte Fraternité Médicale Guinée : +224 621 000 014\n" .
            "• La police DOIT te protéger\n\n" .
            "🏥 SI TU AS DÉJÀ ÉTÉ EXCISÉE :\n" .
            "• Consulte un médecin pour complications éventuelles\n" .
            "• Soutien psychologique disponible gratuitement\n" .
            "• Reconstruction chirurgicale possible (demande info)\n\n" .
            "⚖️ PORTER PLAINTE :\n" .
            "• Tu peux dénoncer les responsables\n" .
            "• Association des Juristes Guinéennes : aide juridique gratuite\n" .
            "• Même ta famille peut être poursuivie si impliquée\n\n" .
            "📞 AIDE SPÉCIALISÉE :\n" .
            "• Fraternité Médicale Guinée : +224 621 000 014\n" .
            "• OPROGEM : 116\n" .
            "• Centre Sabou Guinée : +224 621 000 006\n\n" .
            "⚠️ Ton corps t'appartient. L'excision est une MUTILATION, pas une culture.";
    }

    private function getCyberharcelementAdvice(): string
    {
        return "⚠️ CONSEILS - CYBERHARCÈLEMENT :\n\n" .
            "🛑 PROTÈGE-TOI IMMÉDIATEMENT :\n" .
            "• BLOQUE la personne sur tous les réseaux sociaux\n" .
            "• Mets tes comptes en PRIVÉ temporairement\n" .
            "• Ne réponds PAS aux provocations\n\n" .
            "📱 COLLECTE DES PREUVES :\n" .
            "• CAPTURES D'ÉCRAN de TOUT (messages, posts, commentaires)\n" .
            "• Inclus les dates, heures, noms d'utilisateur\n" .
            "• Sauvegarde dans plusieurs endroits (email, cloud, clé USB)\n" .
            "• NE SUPPRIME RIEN avant d'avoir sauvegardé\n\n" .
            "⚙️ SÉCURITÉ DU COMPTE :\n" .
            "• Change TOUS tes mots de passe\n" .
            "• Active l'authentification à deux facteurs\n" .
            "• Vérifie les appareils connectés à tes comptes\n" .
            "• Révoque l'accès aux applications suspectes\n\n" .
            "📢 SIGNALE :\n" .
            "• Signale le profil sur la plateforme (Facebook, Instagram, etc.)\n" .
            "• Contacte OPROGEM : 116\n" .
            "• Tu peux porter plainte à la police avec les captures d'écran\n\n" .
            "👥 PARLE-EN :\n" .
            "• À un parent, ami.e, enseignant.e de confiance\n" .
            "• Ne reste pas seul.e face au harcèlement\n\n" .
            "⚠️ Le cyberharcèlement est un DÉLIT. Tu n'es pas responsable.";
    }

    private function getRevengePornAdvice(): string
    {
        return "⚠️ CONSEILS URGENTS - DIFFUSION IMAGES INTIMES :\n\n" .
            "🚨 ACTION IMMÉDIATE :\n" .
            "• C'est un CRIME en Guinée (violation vie privée + chantage)\n" .
            "• VA PORTER PLAINTE dès maintenant\n" .
            "• Contacte OPROGEM : 116 pour accompagnement\n\n" .
            "📱 RETRAIT DU CONTENU :\n" .
            "• SIGNALE immédiatement sur la plateforme (Facebook, Instagram, WhatsApp)\n" .
            "• Demande le retrait d'urgence (formulaire spécial pour contenu intime)\n" .
            "• Envoie email à : support@facebook.com, support@instagram.com\n" .
            "• Mentionne : 'non-consensual intimate images' ou 'revenge porn'\n\n" .
            "📝 PREUVES :\n" .
            "• Captures d'écran AVANT que ça soit retiré\n" .
            "• URLs des posts/messages\n" .
            "• Profil de la personne qui a diffusé\n" .
            "• Conversations montrant le chantage/menaces\n\n" .
            "🔒 PROTÈGE-TOI :\n" .
            "• Mets TOUS tes comptes en PRIVÉ\n" .
            "• Change tes mots de passe\n" .
            "• Bloque la personne partout\n" .
            "• Vérifie que tes appareils n'ont pas de spyware\n\n" .
            "⚖️ ACTION LÉGALE :\n" .
            "• Association des Juristes Guinéennes : aide juridique gratuite\n" .
            "• La personne risque la PRISON\n" .
            "• Tu peux demander des dommages-intérêts\n\n" .
            "🧠 SOUTIEN PSYCHOLOGIQUE :\n" .
            "• Centre Sabou : +224 621 000 006\n" .
            "• OPROGEM : 116\n\n" .
            "⚠️ CE N'EST PAS DE TA FAUTE. Envoyer des photos intimes ne justifie PAS leur diffusion.";
    }

    private function getChantageEnLigneAdvice(): string
    {
        return "⚠️ CONSEILS URGENTS - CHANTAGE EN LIGNE :\n\n" .
            "🛑 NE CÈDE JAMAIS AU CHANTAGE :\n" .
            "• Ne paie JAMAIS (ça ne s'arrête jamais)\n" .
            "• N'envoie JAMAIS d'autres photos/vidéos\n" .
            "• Ne fais RIEN de ce qu'on te demande\n\n" .
            "📱 COUPE LE CONTACT :\n" .
            "• BLOQUE immédiatement la personne\n" .
            "• Ne réponds plus à AUCUN message\n" .
            "• Change tes mots de passe\n\n" .
            "📝 PREUVES ESSENTIELLES :\n" .
            "• CAPTURES D'ÉCRAN de TOUTES les conversations de chantage\n" .
            "• Inclus les demandes d'argent/photos/actions\n" .
            "• Note tous les comptes utilisés par le maître-chanteur\n" .
            "• Sauvegarde TOUT dans plusieurs endroits\n\n" .
            "🚨 VA À LA POLICE MAINTENANT :\n" .
            "• Le chantage est un CRIME grave\n" .
            "• La police peut tracer la personne\n" .
            "• Plus tu attends, plus c'est difficile\n\n" .
            "⚖️ AIDE JURIDIQUE :\n" .
            "• Association des Juristes Guinéennes : +224 621 000 013\n" .
            "• OPROGEM : 116\n" .
            "• Centre Sabou : +224 621 000 006\n\n" .
            "🔒 SÉCURISE TES COMPTES :\n" .
            "• Active l'authentification à 2 facteurs PARTOUT\n" .
            "• Vérifie les appareils connectés\n" .
            "• Change tous les mots de passe\n\n" .
            "⚠️ Le chantage NE S'ARRÊTE que si tu portes plainte. N'aie pas peur.";
    }

    private function getCyberstalkingAdvice(): string
    {
        return "⚠️ CONSEILS - CYBERSTALKING / SURVEILLANCE NUMÉRIQUE :\n\n" .
            "🔍 DÉTECTE LA SURVEILLANCE :\n" .
            "• Quelqu'un connaît tes mouvements sans que tu les aies partagés ?\n" .
            "• Tes publications privées sont connues ?\n" .
            "• Tu reçois des messages montrant qu'on te surveille ?\n\n" .
            "📱 VÉRIFIE TES APPAREILS :\n" .
            "• Applications installées récemment (surtout cachées)\n" .
            "• Applications de surveillance : mSpy, FlexiSpy, Spyzie, etc.\n" .
            "• Partage de localisation activé (Google Maps, Find My, Life360)\n" .
            "• Accès iCloud/Google partagé avec quelqu'un\n\n" .
            "🔒 REPRENDS LE CONTRÔLE :\n" .
            "• Change TOUS tes mots de passe depuis un appareil SÛR (pas le tien)\n" .
            "• Déconnecte TOUS les appareils de tes comptes\n" .
            "• Désactive le partage de localisation\n" .
            "• Révoque l'accès aux applications tierces\n" .
            "• Réinitialise ton téléphone en mode usine (après sauvegarde)\n\n" .
            "⚙️ SÉCURITÉ AVANCÉE :\n" .
            "• Active l'authentification à 2 facteurs PARTOUT\n" .
            "• Utilise un nouveau mot de passe UNIQUE pour chaque compte\n" .
            "• Vérifie les emails de connexion suspects\n" .
            "• Change de numéro SIM si nécessaire\n\n" .
            "📝 DOCUMENTE TOUT :\n" .
            "• Captures d'écran des messages de stalking\n" .
            "• Liste des fois où la personne savait ta localisation\n" .
            "• Noms des apps suspectes trouvées\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• Le cyberstalking est un DÉLIT\n" .
            "• Police : 117 (avec preuves)\n" .
            "• OPROGEM : 116\n\n" .
            "⚠️ La surveillance numérique est une forme de CONTRÔLE et de VIOLENCE.";
    }

    private function getUsurpationIdentiteAdvice(): string
    {
        return "⚠️ CONSEILS - USURPATION D'IDENTITÉ EN LIGNE :\n\n" .
            "🚨 ACTION IMMÉDIATE :\n" .
            "• SIGNALE le faux profil sur la plateforme immédiatement\n" .
            "• Signale comme 'usurpation d'identité' ou 'fake account'\n" .
            "• Facebook/Instagram ont des formulaires spéciaux pour ça\n\n" .
            "📢 AVERTIS TON RÉSEAU :\n" .
            "• Poste publiquement que ce n'est PAS ton compte\n" .
            "• Préviens tes amis/famille de ne pas accepter ou interagir\n" .
            "• Demande-leur de signaler le faux profil aussi\n\n" .
            "📝 COLLECTE DES PREUVES :\n" .
            "• Captures d'écran du faux profil (URL visible)\n" .
            "• Captures des fausses publications\n" .
            "• Messages reçus par la fausse identité\n" .
            "• Profils des personnes contactées par le faux compte\n\n" .
            "🔒 SÉCURISE TES COMPTES RÉELS :\n" .
            "• Change TOUS tes mots de passe\n" .
            "• Active l'authentification à deux facteurs\n" .
            "• Vérifie les paramètres de confidentialité\n" .
            "• Limite qui peut voir tes photos\n\n" .
            "⚖️ ACTION LÉGALE :\n" .
            "• L'usurpation d'identité est un DÉLIT\n" .
            "• VA PORTER PLAINTE avec les captures d'écran\n" .
            "• Association des Juristes Guinéennes : aide gratuite\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Police : 117\n\n" .
            "⚠️ Agis VITE : plus le faux profil reste actif, plus il peut nuire.";
    }

    private function getHackingAdvice(): string
    {
        return "⚠️ CONSEILS URGENTS - HACKING / VIOLATION VIE PRIVÉE :\n\n" .
            "🚨 LIMITE LES DÉGÂTS :\n" .
            "• Change IMMÉDIATEMENT tes mots de passe depuis un appareil SÛR\n" .
            "• Déconnecte TOUS les appareils de tes comptes\n" .
            "• Active l'authentification à 2 facteurs PARTOUT\n\n" .
            "📧 VÉRIFIE TES COMPTES :\n" .
            "• Email : vérifie les règles de transfert automatique\n" .
            "• Réseaux sociaux : vérifie les applications connectées\n" .
            "• Cloud (Google Drive, iCloud) : vérifie les partages\n" .
            "• Banque en ligne : vérifie les transactions\n\n" .
            "📱 NETTOIE TES APPAREILS :\n" .
            "• Scan antivirus complet\n" .
            "• Supprime les applications suspectes\n" .
            "• Réinitialise en mode usine si nécessaire\n\n" .
            "📝 DOCUMENTE TOUT :\n" .
            "• Captures d'écran des activités suspectes\n" .
            "• Emails de connexion depuis lieux inconnus\n" .
            "• Messages/posts que tu n'as pas envoyés\n" .
            "• Transactions bancaires non autorisées\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• Le hacking est un CRIME grave\n" .
            "• Police : 117 (apporte les preuves)\n" .
            "• Si argent volé : contacte ta banque immédiatement\n\n" .
            "🔐 SÉCURISE À LONG TERME :\n" .
            "• Utilise un gestionnaire de mots de passe\n" .
            "• Ne réutilise JAMAIS le même mot de passe\n" .
            "• Vérifie régulièrement les connexions actives\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Association des Juristes Guinéennes : +224 621 000 013\n\n" .
            "⚠️ Agis VITE. Chaque minute compte pour limiter les dégâts.";
    }

    private function getMenacesEnLigneAdvice(): string
    {
        return "⚠️ CONSEILS - MENACES EN LIGNE :\n\n" .
            "🚨 PRENDS-LE AU SÉRIEUX :\n" .
            "• TOUTE menace doit être prise au sérieux\n" .
            "• Même si tu penses que c'est 'pour rire'\n" .
            "• Les menaces en ligne peuvent devenir réelles\n\n" .
            "📝 PREUVES CRUCIALES :\n" .
            "• CAPTURES D'ÉCRAN de TOUTES les menaces\n" .
            "• Inclus dates, heures, nom d'utilisateur\n" .
            "• Sauvegarde dans plusieurs endroits\n" .
            "• NE SUPPRIME RIEN\n\n" .
            "🛑 NE RÉPONDS PAS :\n" .
            "• Ne réponds JAMAIS aux menaces\n" .
            "• Ça peut aggraver la situation\n" .
            "• Bloque la personne APRÈS avoir fait les captures\n\n" .
            "🚨 VA À LA POLICE MAINTENANT :\n" .
            "• Les menaces sont un DÉLIT\n" .
            "• La police peut intervenir AVANT qu'il se passe quelque chose\n" .
            "• Apporte les captures d'écran\n\n" .
            "🔒 PROTÈGE-TOI :\n" .
            "• Mets tes comptes en PRIVÉ\n" .
            "• Ne partage plus ta localisation publiquement\n" .
            "• Change tes habitudes si menaces physiques\n" .
            "• Informe ton entourage (famille, école, travail)\n\n" .
            "📞 AIDE D'URGENCE :\n" .
            "• Police : 117\n" .
            "• OPROGEM : 116 (24h/24)\n" .
            "• Si danger immédiat : appelle la police directement\n\n" .
            "⚠️ Ne minimise JAMAIS une menace. Mieux vaut alerter pour rien que de ne rien faire.";
    }

    private function getDeepfakeAdvice(): string
    {
        return "⚠️ CONSEILS - DEEPFAKE / MANIPULATION MÉDIA :\n\n" .
            "🚨 C'EST TRÈS GRAVE :\n" .
            "• Les deepfakes sexuels sont un CRIME\n" .
            "• Manipulation d'image pour nuire = violation vie privée\n" .
            "• Porte plainte IMMÉDIATEMENT\n\n" .
            "📝 PREUVES ESSENTIELLES :\n" .
            "• Captures d'écran du contenu manipulé (URL visible)\n" .
            "• Captures des lieux où c'est partagé\n" .
            "• Télécharge le contenu si possible (comme preuve)\n" .
            "• Liste des personnes qui l'ont vu/partagé\n\n" .
            "📱 RETRAIT DU CONTENU :\n" .
            "• SIGNALE immédiatement sur la plateforme\n" .
            "• Mentionne 'manipulated media', 'deepfake', 'fake pornography'\n" .
            "• Demande retrait d'urgence\n" .
            "• Contacte le support de la plateforme directement\n\n" .
            "📢 DÉMENS PUBLIQUEMENT :\n" .
            "• Poste un message clair que c'est FAUX\n" .
            "• Explique que c'est une manipulation\n" .
            "• Demande à ton réseau de ne pas partager\n\n" .
            "⚖️ ACTION LÉGALE URGENTE :\n" .
            "• VA PORTER PLAINTE immédiatement\n" .
            "• Association des Juristes Guinéennes : +224 621 000 013\n" .
            "• La personne risque la PRISON\n" .
            "• Tu peux demander des dommages-intérêts importants\n\n" .
            "🧠 SOUTIEN PSYCHOLOGIQUE :\n" .
            "• Centre Sabou : +224 621 000 006\n" .
            "• OPROGEM : 116\n" .
            "• C'est très traumatisant, ne reste pas seul.e\n\n" .
            "⚠️ Les deepfakes sont une forme de VIOLENCE SEXUELLE. Ce n'est PAS de ta faute.";
    }
}



    /**
     * Génère des conseils spécifiques selon le sous-type de violence numérique
     *
     * @param string $sousTypeName
     * @return string
     */
    private function getAdviceForSousType(string $sousTypeName): string
    {
        return match ($sousTypeName) {
            'Harcèlement sur réseaux sociaux' => $this->getHarcelementReseauxSociauxAdvice(),
            'Harcèlement par messagerie (SMS)' => $this->getHarcelementSMSAdvice(),
            'Chantage avec photos/vidéos intimes (sextorsion)' => $this->getChantageEnLigneAdvice(),
            'Menaces ou insultes répétées en ligne' => $this->getMenacesEnLigneAdvice(),
            'Partage non-consensuel d\'images intimes (revenge porn)' => $this->getRevengePornAdvice(),
            'Surveillance/espionnage via téléphone' => $this->getCyberstalkingAdvice(),
            'Usurpation d\'identité en ligne' => $this->getUsurpationIdentiteAdvice(),
            'Arnaque sentimentale' => $this->getArnaqueSentimentaleAdvice(),
            'Exploitation sexuelle via internet' => $this->getExploitationSexuelleAdvice(),
            'Création de faux profils pour harceler' => $this->getFauxProfilsAdvice(),
            'Autre violence numérique' => $this->getCyberharcelementAdvice(),
            default => $this->getCyberharcelementAdvice(),
        };
    }

    private function getHarcelementReseauxSociauxAdvice(): string
    {
        return "⚠️ CONSEILS - HARCÈLEMENT SUR RÉSEAUX SOCIAUX :\n\n" .
            "🛑 PROTÈGE-TOI IMMÉDIATEMENT :\n" .
            "• BLOQUE la personne sur TOUS les réseaux sociaux\n" .
            "• Mets tes comptes en PRIVÉ temporairement\n" .
            "• Limite qui peut te contacter et commenter\n" .
            "• Ne réponds PAS aux provocations\n\n" .
            "📱 COLLECTE DES PREUVES :\n" .
            "• CAPTURES D'ÉCRAN de TOUT (messages, posts, commentaires)\n" .
            "• Inclus les dates, heures, noms d'utilisateur visibles\n" .
            "• Sauvegarde dans plusieurs endroits (email, cloud, clé USB)\n" .
            "• NE SUPPRIME RIEN avant d'avoir sauvegardé\n\n" .
            "📢 SIGNALE SUR LA PLATEFORME :\n" .
            "• Facebook : Menu (3 points) > Signaler > Harcèlement\n" .
            "• Instagram : ... > Signaler > C'est du harcèlement ou intimidation\n" .
            "• TikTok : Partager > Signaler > Harcèlement\n" .
            "• Twitter/X : ... > Signaler le tweet > Comportement abusif\n\n" .
            "⚙️ SÉCURITÉ DU COMPTE :\n" .
            "• Change TOUS tes mots de passe\n" .
            "• Active l'authentification à deux facteurs\n" .
            "• Vérifie les appareils connectés à tes comptes\n" .
            "• Révoque l'accès aux applications tierces suspectes\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• Le harcèlement en ligne est un DÉLIT\n" .
            "• Police : 117 (apporte les captures d'écran)\n" .
            "• OPROGEM : 116 pour accompagnement\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116 (24h/24)\n" .
            "• Centre Sabou : +224 621 000 006\n\n" .
            "⚠️ Le harcèlement n'est JAMAIS acceptable. Tu as le droit d'être en sécurité en ligne.";
    }

    private function getHarcelementSMSAdvice(): string
    {
        return "⚠️ CONSEILS - HARCÈLEMENT PAR MESSAGERIE (SMS) :\n\n" .
            "🛑 PROTÈGE-TOI :\n" .
            "• BLOQUE le numéro immédiatement\n" .
            "• Active le filtre anti-spam de ton opérateur\n" .
            "• Ne réponds JAMAIS aux messages\n\n" .
            "📱 COLLECTE DES PREUVES :\n" .
            "• CAPTURES D'ÉCRAN de TOUS les SMS (avec numéro et date visibles)\n" .
            "• Note les heures et fréquence des messages\n" .
            "• Sauvegarde dans plusieurs endroits\n" .
            "• NE SUPPRIME RIEN\n\n" .
            "📞 CONTACTE TON OPÉRATEUR :\n" .
            "• Orange Guinée : 111\n" .
            "• MTN Guinée : 1000\n" .
            "• Cellcom : 122\n" .
            "• Demande le blocage du numéro et historique des appels\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• Le harcèlement par SMS est un DÉLIT\n" .
            "• Police : 117 (apporte les captures d'écran)\n" .
            "• L'opérateur peut fournir les logs d'appels à la police\n\n" .
            "⚙️ OPTIONS TECHNIQUES :\n" .
            "• Change de numéro si nécessaire (opérateur peut aider)\n" .
            "• Utilise une app de blocage d'appels (Truecaller, etc.)\n" .
            "• Ne partage ton nouveau numéro qu'avec des personnes de confiance\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Police : 117\n\n" .
            "⚠️ Personne n'a le droit de te harceler. Protège-toi.";
    }

    private function getArnaqueSentimentaleAdvice(): string
    {
        return "⚠️ CONSEILS - ARNAQUE SENTIMENTALE :\n\n" .
            "🚨 SIGNES D'ARNAQUE :\n" .
            "• Déclaration d'amour très rapide\n" .
            "• Refuse de se rencontrer ou de faire un appel vidéo\n" .
            "• Demande d'argent (urgence médicale, voyage, etc.)\n" .
            "• Photos qui semblent professionnelles ou trop parfaites\n" .
            "• Histoire personnelle qui semble trop dramatique\n\n" .
            "🛑 ARRÊTE IMMÉDIATEMENT :\n" .
            "• N'envoie JAMAIS d'argent\n" .
            "• Ne partage AUCUNE information bancaire\n" .
            "• Ne donne pas de photos intimes\n" .
            "• BLOQUE la personne sur toutes les plateformes\n\n" .
            "🔍 VÉRIFIE L'IDENTITÉ :\n" .
            "• Recherche inversée d'image Google (les arnaqueurs utilisent des photos volées)\n" .
            "• Vérifie les profils sociaux (souvent récents avec peu d'amis)\n" .
            "• Demande un appel vidéo immédiat (les arnaqueurs refusent)\n\n" .
            "📝 PREUVES :\n" .
            "• Captures d'écran de TOUTES les conversations\n" .
            "• Profil de la personne\n" .
            "• Demandes d'argent ou informations bancaires\n" .
            "• Relevés bancaires si tu as déjà envoyé de l'argent\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• L'arnaque sentimentale est un CRIME\n" .
            "• Police : 117 (apporte toutes les preuves)\n" .
            "• Contacte ta banque si tu as envoyé de l'argent\n\n" .
            "💡 PRÉVENTION FUTURE :\n" .
            "• Méfie-toi des rencontres qui progressent trop vite\n" .
            "• Ne partage jamais d'informations financières en ligne\n" .
            "• Toujours vérifier l'identité avant de faire confiance\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Police cybercriminalité : 117\n\n" .
            "⚠️ L'amour véritable ne demande pas d'argent. Si c'est trop beau pour être vrai, c'est probablement une arnaque.";
    }

    private function getExploitationSexuelleAdvice(): string
    {
        return "⚠️ CONSEILS URGENTS - EXPLOITATION SEXUELLE VIA INTERNET :\n\n" .
            "🚨 C'EST TRÈS GRAVE :\n" .
            "• L'exploitation sexuelle est un CRIME grave\n" .
            "• Tu es une VICTIME, pas une criminelle\n" .
            "• L'aide existe et est GRATUITE\n\n" .
            "🆘 AIDE IMMÉDIATE :\n" .
            "• OPROGEM : 116 (24h/24, confidentiel)\n" .
            "• Centre Sabou Guinée : +224 621 000 006\n" .
            "• Police : 117 (tu seras protégée, pas jugée)\n\n" .
            "📝 SI TU VEUX PORTER PLAINTE :\n" .
            "• Collecte TOUTES les preuves (messages, profils, photos)\n" .
            "• Note tous les détails (noms, lieux, dates)\n" .
            "• L'Association des Juristes Guinéennes peut t'accompagner gratuitement\n" .
            "• Tu peux porter plainte de manière anonyme au début\n\n" .
            "🔒 PROTÈGE-TOI :\n" .
            "• Change TOUS tes mots de passe\n" .
            "• Bloque les personnes impliquées\n" .
            "• Ne supprime AUCUNE preuve\n" .
            "• Mets tes comptes en privé\n\n" .
            "🏥 SOUTIEN MÉDICAL ET PSYCHOLOGIQUE :\n" .
            "• Guichet Unique VBG CHU Donka : +224 621 000 007 (gratuit)\n" .
            "• Soins médicaux gratuits si nécessaire\n" .
            "• Accompagnement psychologique\n" .
            "• Tout est CONFIDENTIEL\n\n" .
            "⚖️ TES DROITS :\n" .
            "• Les exploiteurs risquent de LOURDES peines de prison\n" .
            "• Tu peux demander des dommages-intérêts\n" .
            "• Tu seras protégée pendant la procédure\n" .
            "• Ton identité peut rester confidentielle\n\n" .
            "📞 URGENCES :\n" .
            "• OPROGEM : 116 (24h/24)\n" .
            "• Police : 117\n" .
            "• Centre Sabou : +224 621 000 006\n\n" .
            "⚠️ Tu n'es PAS seule. Ce n'est PAS de ta faute. L'aide existe.";
    }

    private function getFauxProfilsAdvice(): string
    {
        return "⚠️ CONSEILS - CRÉATION DE FAUX PROFILS POUR HARCELER :\n\n" .
            "🚨 IDENTIFIE LES FAUX PROFILS :\n" .
            "• Profil récent avec peu d'amis\n" .
            "• Utilise tes photos ou ton nom\n" .
            "• Contacts répétés de comptes différents\n" .
            "• Messages similaires de profils différents\n\n" .
            "📢 SIGNALE IMMÉDIATEMENT :\n" .
            "• Sur chaque plateforme : Signaler > Faux compte\n" .
            "• Facebook : Formulaire spécial pour usurpation d'identité\n" .
            "• Instagram : Signaler > C'est un faux compte\n" .
            "• Demande le retrait urgent du profil\n\n" .
            "📱 PRÉVIENS TON RÉSEAU :\n" .
            "• Poste publiquement que ces comptes sont FAUX\n" .
            "• Demande à tes amis de signaler aussi\n" .
            "• Ne pas accepter ou interagir avec ces profils\n" .
            "• Partage la liste des faux comptes identifiés\n\n" .
            "📝 COLLECTE DES PREUVES :\n" .
            "• Captures d'écran de TOUS les faux profils (URL visible)\n" .
            "• Captures des messages reçus\n" .
            "• Liste de tous les comptes suspects\n" .
            "• Sauvegarde dans plusieurs endroits\n\n" .
            "🔒 PROTÈGE TES COMPTES RÉELS :\n" .
            "• Mets tes comptes en PRIVÉ temporairement\n" .
            "• Limite qui peut voir tes photos et infos\n" .
            "• Active l'authentification à deux facteurs\n" .
            "• Ajoute un watermark sur tes photos publiques\n\n" .
            "🚨 PORTE PLAINTE :\n" .
            "• La création de faux profils pour harceler est un DÉLIT\n" .
            "• Police : 117 (apporte les captures d'écran)\n" .
            "• OPROGEM : 116 pour accompagnement\n\n" .
            "⚖️ ACTION LÉGALE :\n" .
            "• Association des Juristes Guinéennes : +224 621 000 013\n" .
            "• Les harceleurs risquent des poursuites\n" .
            "• Tu peux demander des dommages-intérêts\n\n" .
            "📞 AIDE :\n" .
            "• OPROGEM : 116\n" .
            "• Police : 117\n\n" .
            "⚠️ Agis VITE pour faire retirer les faux profils avant qu'ils ne causent plus de dégâts.";
    }
}
