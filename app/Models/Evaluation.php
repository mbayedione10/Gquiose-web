<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'utilisateur_id',
        'contexte',
        'contexte_id',
        'reponses',
        'score_global',
        'commentaire',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'reponses' => 'array',
        'score_global' => 'decimal:2',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function reponsesDetails()
    {
        return $this->hasMany(ReponseEvaluation::class);
    }

    public function getContexteNomAttribute()
    {
        $contextes = [
            'quiz' => 'Quiz',
            'article' => 'Article',
            'structure' => 'Structure',
            'generale' => 'Évaluation générale',
            'alerte' => 'Alerte',
        ];

        return $contextes[$this->contexte] ?? $this->contexte;
    }
}
