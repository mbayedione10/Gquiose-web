<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieConseil extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'categorie_conseils';

    protected $fillable = [
        'nom',
        'description',
        'emoji',
        'type_alerte_id',
        'sous_type_violence_numerique_id',
        'is_default',
        'ordre',
        'status',
    ];

    protected $searchableFields = ['nom', 'description'];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Relation avec TypeAlerte
     */
    public function typeAlerte(): BelongsTo
    {
        return $this->belongsTo(TypeAlerte::class, 'type_alerte_id');
    }

    /**
     * Relation avec SousTypeViolenceNumerique
     */
    public function sousTypeViolenceNumerique(): BelongsTo
    {
        return $this->belongsTo(SousTypeViolenceNumerique::class, 'sous_type_violence_numerique_id');
    }

    /**
     * Relation avec les sections
     */
    public function sections(): HasMany
    {
        return $this->hasMany(SectionConseil::class, 'categorie_conseil_id');
    }

    /**
     * Scope pour les catégories actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope pour la catégorie par défaut
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Retourne le libellé du type associé (TypeAlerte ou SousType)
     */
    public function getTypeLabelAttribute(): string
    {
        if ($this->sousTypeViolenceNumerique) {
            return 'Sous-type: ' . $this->sousTypeViolenceNumerique->nom;
        }

        if ($this->typeAlerte) {
            return 'Type: ' . $this->typeAlerte->name;
        }

        return $this->is_default ? 'Par défaut' : 'Non assigné';
    }
}
