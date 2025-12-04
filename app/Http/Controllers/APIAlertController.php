<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Mail\NotificationEmail;
use App\Models\Alerte;
use App\Models\Information;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class APIAlertController extends Controller
{
    /**
     * Liste toutes les alertes
     * GET /api/v1/alertes (toutes les alertes)
     * GET /api/v1/alertes?user_id={user_id} (alertes d'un utilisateur spécifique)
     */
    public function index(Request $request)
    {
        $userId = $request->query('user_id');

        // Query de base avec les relations
        $query = Alerte::with(['typeAlerte', 'ville', 'utilisateur'])
            ->orderByDesc('created_at');

        // Filtrer par user_id si fourni
        if ($userId) {
            $user = Utilisateur::find($userId);

            if (!$user) {
                return ApiResponse::error("Cet utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
            }

            $query->where('utilisateur_id', $userId);
        }

        // Récupérer les alertes
        $alertes = $query->get()
            ->map(function ($alerte) {
                return [
                    'id' => $alerte->id,
                    'ref' => $alerte->ref,
                    'etat' => $alerte->etat,
                    'numero_suivi' => $alerte->numero_suivi,
                    'created_at' => $alerte->created_at,

                    // Utilisateur associé
                    'utilisateur' => [
                        'id' => $alerte->utilisateur->id,
                        'name' => $alerte->utilisateur->name,
                        'email' => $alerte->utilisateur->email,
                        'phone' => $alerte->utilisateur->phone,
                    ],

                    // Informations générales
                    'informations_generales' => [
                        'type_alerte' => $alerte->typeAlerte ? $alerte->typeAlerte->name : null,
                        'description' => $alerte->description,
                        'ville' => $alerte->ville ? $alerte->ville->name : null,
                        'latitude' => $alerte->latitude,
                        'longitude' => $alerte->longitude,
                        'precision_localisation' => $alerte->precision_localisation,
                        'rayon_approximation_km' => $alerte->rayon_approximation_km,
                        'quartier' => $alerte->quartier,
                        'commune' => $alerte->commune,
                    ],

                    // Violences numériques
                    'violences_numeriques' => [
                        'plateformes' => $alerte->plateformes,
                        'nature_contenu' => $alerte->nature_contenu,
                        'urls_problematiques' => $alerte->urls_problematiques,
                        'comptes_impliques' => $alerte->comptes_impliques,
                        'frequence_incidents' => $alerte->frequence_incidents,
                    ],

                    // Détails incident
                    'details_incident' => [
                        'date_incident' => $alerte->date_incident,
                        'heure_incident' => $alerte->heure_incident,
                        'relation_agresseur' => $alerte->relation_agresseur,
                        'impact' => $alerte->impact,
                    ],

                    // Preuves & Conseils
                    'preuves_conseils' => [
                        'preuves' => $alerte->preuves,
                        'conseils_securite' => $alerte->conseils_securite,
                        'conseils_lus' => $alerte->conseils_lus,
                    ],

                    // Consentement
                    'consentement' => [
                        'anonymat_souhaite' => $alerte->anonymat_souhaite,
                        'consentement_transmission' => $alerte->consentement_transmission,
                    ],
                ];
            });

        return ApiResponse::success([
            'alertes' => $alertes,
            'total' => $alertes->count()
        ]);
    }

    public function  sync(Request $request)
    {
        if (!isset($request['user_id']) &&  !isset($request['type']))
        {
            return ApiResponse::error("les champs sont obligatoires", Response::HTTP_BAD_REQUEST);
        }

        $user = Utilisateur::whereId($request['user_id'])->first();

        if ($user == null)
            return ApiResponse::error("Cet utilisateur n'existe pas");


        $types = [ "Mutilation génitale", "Viol", "Mariage précoce", "Autres"];

        if (!in_array($request['type'], $types))
            return ApiResponse::error("Le type n'est pas correct", Response::HTTP_BAD_REQUEST);


        $alerte = new Alerte();
        $alerte->ref = uniqid();
        $alerte->utilisateur_id = $user->id;
        $alerte->type = $request['type'];
        $alerte->description = $request['description'];
        $alerte->etat = "Non approuvée";
        $alerte->save();

        $info = Information::first();

        if ($info != null && $info->email_alerte != null)
        {
            $objet = "Nouvelle alerte signalée";
            $greeting = "Bonjour ";
            $content = "Une nouvelle alerte vient d'être signalée. \n\n";
            $content .= "Ref: " .$alerte->ref."\n\n";
            $content .= "Type: " .$alerte->type."\n\n";

            if ($alerte->description != null)
                $content .= "Description: " .$alerte->description."\n\n";

            $content .= "Utilisateur: " .$user->name.  "\n\n";
            $content .= "Numéro de téléphone: " .$user->phone.  "\n\n";
            $content .= "Courriel: " .$user->email.  "\n\n";

            $emails = $info->email_alerte;
            $first = $emails[0];

            $others = array_slice($emails, 1);

            Mail::to($first)
                ->cc($others)
                ->send(new NotificationEmail($greeting, $objet, $content));
        }



        $data = [
            "ref" => $alerte->ref,
            "type" => $alerte->type,
            "etat" => $alerte->etat,
        ];

        return ApiResponse::success($data);

    }
}
