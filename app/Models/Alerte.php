<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'ref',
        'description',
        'latitude',
        'longitude',
        'precision_localisation',
        'rayon_approximation_km',
        'quartier',
        'commune',
        'type_alerte_id',
        'sous_type_violence_numerique_id',
        'etat',
        'ville_id',
        'utilisateur_id',
        'preuves',
        'conseils_securite',
        'conseils_lus',
        // Champs spécifiques violences numériques
        'plateformes',
        'nature_contenu',
        'urls_problematiques',
        'comptes_impliques',
        'frequence_incidents',
        // Informations générales incident
        'date_incident',
        'heure_incident',
        'relation_agresseur',
        'impact',
        // Consentement et anonymat
        'anonymat_souhaite',
        'consentement_transmission',
        'numero_suivi',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'preuves' => 'array',
        'conseils_lus' => 'boolean',
        'plateformes' => 'array',
        'nature_contenu' => 'array',
        'impact' => 'array',
        'anonymat_souhaite' => 'boolean',
        'consentement_transmission' => 'boolean',
        'date_incident' => 'date',
        'heure_incident' => 'datetime:H:i',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function typeAlerte()
    {
        return $this->belongsTo(TypeAlerte::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class);
    }

    public function sousTypeViolenceNumerique()
    {
        return $this->belongsTo(SousTypeViolenceNumerique::class, 'sous_type_violence_numerique_id');
    }
}
