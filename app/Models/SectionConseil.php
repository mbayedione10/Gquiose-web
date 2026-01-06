<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionConseil extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'section_conseils';

    protected $fillable = [
        'categorie_conseil_id',
        'titre',
        'emoji',
        'ordre',
        'status',
    ];

    protected $searchableFields = ['titre'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relation avec la catégorie parente
     */
    public function categorieConseil(): BelongsTo
    {
        return $this->belongsTo(CategorieConseil::class, 'categorie_conseil_id');
    }

    /**
     * Alias pour la relation catégorie (pour compatibilité)
     */
    public function categorie(): BelongsTo
    {
        return $this->categorieConseil();
    }

    /**
     * Relation avec les items
     */
    public function items(): HasMany
    {
        return $this->hasMany(ItemConseil::class, 'section_conseil_id');
    }

    /**
     * Scope pour les sections actives
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
     * Retourne le titre formaté avec emoji
     */
    public function getFormattedTitleAttribute(): string
    {
        if ($this->emoji) {
            return "{$this->emoji} {$this->titre}";
        }

        return $this->titre;
    }
}
