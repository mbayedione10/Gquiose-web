<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemConseil extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'item_conseils';

    protected $fillable = [
        'section_conseil_id',
        'contenu',
        'ordre',
        'status',
    ];

    protected $searchableFields = ['contenu'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relation avec la section parente
     */
    public function sectionConseil(): BelongsTo
    {
        return $this->belongsTo(SectionConseil::class, 'section_conseil_id');
    }

    /**
     * Alias pour la relation section (pour compatibilité)
     */
    public function section(): BelongsTo
    {
        return $this->sectionConseil();
    }

    /**
     * Scope pour les items actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope pour ordonner par ordre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre');
    }

    /**
     * Retourne le contenu tronqué pour l'affichage en liste
     */
    public function getTruncatedContentAttribute(): string
    {
        return \Str::limit($this->contenu, 80);
    }

    /**
     * Accès à la catégorie via la section
     */
    public function getCategorieAttribute(): ?CategorieConseil
    {
        return $this->sectionConseil?->categorieConseil;
    }
}
