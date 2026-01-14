<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlerteStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ref' => ['required', 'unique:alertes,ref', 'max:255', 'string'],
            'description' => ['required', 'max:1000', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'type_alerte_id' => ['required', 'exists:type_alertes,id'],
            'etat' => ['required', 'max:255', 'string'],
            'ville_id' => ['required', 'exists:villes,id'],
            'utilisateur_id' => ['required', 'exists:utilisateurs,id'],

            // Preuves (evidence files) - maximum 5 fichiers
            'preuves' => ['nullable', 'array', 'max:5'],
            'preuves.*' => [
                'file',
                'max:10240', // 10 MB max par fichier
                'mimes:jpeg,jpg,png,pdf,mp4,mov,avi,doc,docx',
            ],

            // === Champs spécifiques violences numériques ===
            'plateformes' => ['nullable', 'array'],
            'plateformes.*' => ['string', 'in:facebook,whatsapp,instagram,tiktok,telegram,snapchat,twitter,sms,appels,email,site_web,app_rencontre,jeu_en_ligne,autre'],

            'nature_contenu' => ['nullable', 'array'],
            'nature_contenu.*' => ['string', 'in:messages_texte,images_photos,videos,messages_vocaux,appels_repetés,publications_publiques,messages_prives,partages_non_autorises,autre'],

            'urls_problematiques' => ['nullable', 'string', 'max:2000'],
            'comptes_impliques' => ['nullable', 'string', 'max:1000'],

            'frequence_incidents' => ['nullable', 'in:unique,quotidien,hebdomadaire,mensuel,continu'],

            // === Informations générales incident ===
            'date_incident' => ['nullable', 'date', 'before_or_equal:today'],
            'heure_incident' => ['nullable', 'date_format:H:i'],
            'relation_agresseur' => ['nullable', 'string', 'max:100', 'in:conjoint,ex_partenaire,famille,collegue,ami,connaissance,inconnu,autre'],

            'impact' => ['nullable', 'array'],
            'impact.*' => ['string', 'in:stress_anxiete,peur_securite,depression,problemes_sommeil,isolement_social,difficultes_professionnelles,autre'],

            // === Consentement et anonymat ===
            'anonymat_souhaite' => ['nullable', 'boolean'],
            'consentement_transmission' => ['required', 'boolean', 'accepted'],

            // Les conseils de sécurité et numéro de suivi sont générés automatiquement
            'conseils_lus' => ['nullable', 'boolean'],
        ];
    }
}
