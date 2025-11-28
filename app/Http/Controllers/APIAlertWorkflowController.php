<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Alerte;
use App\Models\TypeAlerte;
use App\Models\SousTypeViolenceNumerique;
use App\Models\Plateforme;
use App\Models\NatureContenu;
use App\Models\Structure;
use App\Models\Utilisateur;
use App\Services\VBG\SafetyAdviceService;
use App\Services\VBG\EvidenceSecurityService;
use App\Services\VBG\SecureLocationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class APIAlertWorkflowController extends Controller
{
    protected $safetyAdviceService;

    public function __construct(SafetyAdviceService $safetyAdviceService)
    {
        $this->safetyAdviceService = $safetyAdviceService;
    }

    /**
     * Étape 1 : Sélection du type de violence
     * POST /api/v1/alertes/step1
     */
    public function step1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'type_alerte_id' => 'required|exists:type_alertes,id',
            'sous_type_violence_numerique_id' => 'nullable|exists:sous_type_violence_numeriques,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $user = Utilisateur::find($request->utilisateur_id);
        $typeAlerte = TypeAlerte::find($request->type_alerte_id);

        // Créer une alerte en brouillon
        $alerte = new Alerte();
        $alerte->ref = 'ALRT-' . date('Y') . '-' . str_pad(Alerte::count() + 1, 6, '0', STR_PAD_LEFT);
        $alerte->numero_suivi = 'VBG-' . date('Y') . '-' . str_pad(Alerte::count() + 1, 6, '0', STR_PAD_LEFT);
        $alerte->utilisateur_id = $user->id;
        $alerte->type_alerte_id = $typeAlerte->id;
        $alerte->sous_type_violence_numerique_id = $request->sous_type_violence_numerique_id;
        $alerte->etat = 'Brouillon';
        $alerte->description = ''; // Sera complété à l'étape 3
        $alerte->save();

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'ref' => $alerte->ref,
            'numero_suivi' => $alerte->numero_suivi,
            'type_alerte' => $typeAlerte->name,
            'next_step' => $request->sous_type_violence_numerique_id ? 'step2' : 'step3',
            'message' => 'Type de violence enregistré avec succès'
        ]);
    }

    /**
     * Étape 2 : Détails violence numérique (si applicable)
     * POST /api/v1/alertes/step2
     */
    public function step2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alerte_id' => 'required|exists:alertes,id',
            'plateformes' => 'required|array|min:1',
            'plateformes.*' => 'string',
            'nature_contenu' => 'required|array|min:1',
            'nature_contenu.*' => 'string',
            'urls_problematiques' => 'nullable|string|max:2000',
            'comptes_impliques' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $alerte = Alerte::find($request->alerte_id);

        if (!$alerte || $alerte->etat !== 'Brouillon') {
            return ApiResponse::error("Alerte introuvable ou déjà soumise", Response::HTTP_NOT_FOUND);
        }

        $alerte->plateformes = $request->plateformes;
        $alerte->nature_contenu = $request->nature_contenu;
        $alerte->urls_problematiques = $request->urls_problematiques;
        $alerte->comptes_impliques = $request->comptes_impliques;
        $alerte->save();

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'next_step' => 'step3',
            'message' => 'Détails de la violence numérique enregistrés'
        ]);
    }

    /**
     * Étape 3 : Informations détaillées de l'incident
     * POST /api/v1/alertes/step3
     */
    public function step3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alerte_id' => 'required|exists:alertes,id',
            'description' => 'required|string|max:1000',
            'date_incident' => 'nullable|date|before_or_equal:today',
            'heure_incident' => 'nullable|date_format:H:i',
            'relation_agresseur' => 'nullable|string|in:conjoint,ex_partenaire,famille,collegue,ami,connaissance,inconnu,autre',
            'frequence_incidents' => 'nullable|in:unique,quotidien,hebdomadaire,mensuel,continu',
            'impact' => 'nullable|array',
            'impact.*' => 'string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ville_id' => 'nullable|exists:villes,id',
            'preuves' => 'nullable|array|max:5',
            'preuves.*' => 'file|max:10240|mimes:jpeg,jpg,png,pdf,mp4,mov,avi',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $alerte = Alerte::find($request->alerte_id);

        if (!$alerte || $alerte->etat !== 'Brouillon') {
            return ApiResponse::error("Alerte introuvable ou déjà soumise", Response::HTTP_NOT_FOUND);
        }

        // Mise à jour avec les informations détaillées
        $validated = $request->validate([
            'date_incident' => 'nullable|date',
            'heure_incident' => 'nullable|date_format:H:i',
            'relation_agresseur' => 'nullable|string|in:conjoint,ex_partenaire,famille,collegue,ami,connaissance,inconnu,autre',
            'impact' => 'nullable|array',
            'impact.*' => 'string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'ville_id' => 'nullable|exists:villes,id',
        ]);

        // Anonymiser la géolocalisation si fournie
        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $locationService = new SecureLocationService();

            // Valider les coordonnées
            $validation = $locationService->validateCoordinates(
                $validated['latitude'],
                $validated['longitude']
            );

            if (!$validation['valid']) {
                return response::error('Coordonnées GPS invalides: ' . implode(', ', $validation['errors']), 422);
            }

            // Préparer la localisation sécurisée (avec anonymisation)
            $secureLocation = $locationService->prepareSecureLocation(
                $validated['latitude'],
                $validated['longitude'],
                $validated['ville_id'] ?? null,
                anonymize: true // Toujours anonymiser pour protéger la victime
            );

            // Remplacer les coordonnées par les coordonnées anonymisées
            $validated['latitude'] = $secureLocation['latitude'];
            $validated['longitude'] = $secureLocation['longitude'];
            $validated['precision_localisation'] = $secureLocation['precision'];
            $validated['rayon_approximation_km'] = $secureLocation['radius_km'];
            $validated['quartier'] = $secureLocation['quartier'];
            $validated['commune'] = $secureLocation['commune'];

            // Utiliser la ville détectée si non fournie
            if (!$validated['ville_id'] && $secureLocation['ville_id']) {
                $validated['ville_id'] = $secureLocation['ville_id'];
            }
        }

        $alerte->update($validated);


        $alerte->description = $request->description;
        $alerte->frequence_incidents = $request->frequence_incidents;
        $alerte->impact = $request->impact;


        // Gestion sécurisée des preuves uploadées avec chiffrement et suppression EXIF
        if ($request->hasFile('preuves')) {
            $evidenceService = app(\App\Services\VBG\EvidenceSecurityService::class);
            $preuves = [];

            foreach ($request->file('preuves') as $file) {
                $secureEvidence = $evidenceService->secureUpload($file, $alerte->ref);
                $preuves[] = $secureEvidence;
            }

            $alerte->preuves = $preuves;
        }

        $alerte->save();

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'next_step' => 'step4',
            'message' => 'Informations de l\'incident enregistrées de manière sécurisée'
        ]);
    }

    /**
     * Étape 4 : Conseils automatiques personnalisés
     * GET /api/v1/alertes/step4/{alerte_id}
     */
    public function step4($alerte_id)
    {
        $alerte = Alerte::with(['typeAlerte', 'sousTypeViolenceNumerique'])->find($alerte_id);

        if (!$alerte || $alerte->etat !== 'Brouillon') {
            return ApiResponse::error("Alerte introuvable ou déjà soumise", Response::HTTP_NOT_FOUND);
        }

        // Générer les conseils de sécurité automatiques
        $conseils = $this->safetyAdviceService->getAdviceForAlert($alerte);

        $alerte->conseils_securite = $conseils;
        $alerte->save();

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'conseils_securite' => $conseils,
            'next_step' => 'step5',
            'message' => 'Voici des conseils de sécurité personnalisés pour vous'
        ]);
    }

    /**
     * Étape 5 : Orientation et ressources disponibles
     * GET /api/v1/alertes/step5/{alerte_id}
     */
    public function step5($alerte_id)
    {
        $alerte = Alerte::with(['ville'])->find($alerte_id);

        if (!$alerte || $alerte->etat !== 'Brouillon') {
            return ApiResponse::error("Alerte introuvable ou déjà soumise", Response::HTTP_NOT_FOUND);
        }

        $structures = [];
        $numeros_urgence = [];

        // Structures à proximité (si géolocalisation disponible)
        if ($alerte->latitude && $alerte->longitude) {
            $structures = Structure::select('structures.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$alerte->latitude, $alerte->longitude, $alerte->latitude]
                )
                ->having('distance', '<', 50) // 50 km de rayon
                ->orderBy('distance')
                ->with('typeStructure')
                ->limit(10)
                ->get();
        } elseif ($alerte->ville_id) {
            // Structures de la même ville
            $structures = Structure::where('ville_id', $alerte->ville_id)
                ->with('typeStructure')
                ->limit(10)
                ->get();
        }

        // Récupérer les numéros d'urgence depuis la table informations
        $info = \App\Models\Information::first();
        if ($info) {
            $numeros_urgence = [
                'hotline_vbg' => $info->numero_hotline ?? null,
                'police' => $info->numero_police ?? '122',
                'samu' => $info->numero_samu ?? '144',
            ];
        }

        // Plateformes de signalement avec leurs URLs
        $plateformes_signalement = Plateforme::whereNotNull('signalement_url')->get();

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'structures_disponibles' => $structures,
            'numeros_urgence' => $numeros_urgence,
            'plateformes_signalement' => $plateformes_signalement,
            'next_step' => 'step6',
            'message' => 'Voici les ressources disponibles pour vous aider'
        ]);
    }

    /**
     * Étape 6 : Consentement et transmission finale
     * POST /api/v1/alertes/step6
     */
    public function step6(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alerte_id' => 'required|exists:alertes,id',
            'anonymat_souhaite' => 'required|boolean',
            'consentement_transmission' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        }

        $alerte = Alerte::find($request->alerte_id);

        if (!$alerte || $alerte->etat !== 'Brouillon') {
            return ApiResponse::error("Alerte introuvable ou déjà soumise", Response::HTTP_NOT_FOUND);
        }

        $alerte->anonymat_souhaite = $request->anonymat_souhaite;
        $alerte->consentement_transmission = $request->consentement_transmission;
        $alerte->etat = 'Non approuvée'; // Passe de Brouillon à Non approuvée
        $alerte->save();

        // Envoyer notification email si configuré
        $info = \App\Models\Information::first();
        if ($info && $info->email_alerte) {
            try {
                $user = $alerte->utilisateur;
                $objet = "Nouvelle alerte VBG signalée";
                $greeting = "Bonjour ";
                $content = "Une nouvelle alerte VBG vient d'être signalée.\n\n";
                $content .= "Numéro de suivi: " . $alerte->numero_suivi . "\n";
                $content .= "Réf: " . $alerte->ref . "\n";
                $content .= "Type: " . $alerte->typeAlerte->name . "\n\n";

                if ($alerte->sousTypeViolenceNumerique) {
                    $content .= "Sous-type: " . $alerte->sousTypeViolenceNumerique->nom . "\n\n";
                }

                $content .= "Description: " . $alerte->description . "\n\n";

                if (!$alerte->anonymat_souhaite) {
                    $content .= "Utilisateur: " . $user->name . "\n";
                    $content .= "Téléphone: " . $user->phone . "\n";
                    $content .= "Email: " . $user->email . "\n";
                } else {
                    $content .= "Signalement anonyme\n";
                }

                $emails = $info->email_alerte;
                $first = $emails[0];
                $others = array_slice($emails, 1);

                \Mail::to($first)
                    ->cc($others)
                    ->send(new \App\Mail\NotificationEmail($greeting, $objet, $content));
            } catch (\Exception $e) {
                \Log::error('Erreur envoi email alerte: ' . $e->getMessage());
            }
        }

        return ApiResponse::success([
            'alerte_id' => $alerte->id,
            'numero_suivi' => $alerte->numero_suivi,
            'ref' => $alerte->ref,
            'etat' => $alerte->etat,
            'conseils_securite' => $alerte->conseils_securite,
            'message' => 'Votre signalement a été enregistré avec succès. Conservez votre numéro de suivi : ' . $alerte->numero_suivi,
            'ressources_urgence' => [
                'hotline_vbg' => $info->numero_hotline ?? null,
                'police' => '122',
            ]
        ]);
    }

    /**
     * Récupérer les options pour chaque étape
     * GET /api/v1/alertes/workflow-options
     */
    public function getWorkflowOptions()
    {
        return ApiResponse::success([
            'types_alerte' => TypeAlerte::all(['id', 'name']),
            'sous_types_violence_numerique' => SousTypeViolenceNumerique::all(['id', 'nom', 'description']),
            'plateformes' => Plateforme::all(['id', 'nom']),
            'natures_contenu' => NatureContenu::all(['id', 'nom']),
            'relations_agresseur' => [
                'conjoint', 'ex_partenaire', 'famille', 'collegue',
                'ami', 'connaissance', 'inconnu', 'autre'
            ],
            'frequences' => ['unique', 'quotidien', 'hebdomadaire', 'mensuel', 'continu'],
            'impacts' => [
                'stress_anxiete', 'peur_securite', 'depression',
                'problemes_sommeil', 'isolement_social',
                'difficultes_professionnelles', 'autre'
            ]
        ]);
    }
}