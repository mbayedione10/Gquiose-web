<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alerte extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'ref',
        'description',
        'latitude',
        'longitude',
        'type_alerte_id',
        'etat',
        'ville_id',
        'utilisateur_id',
        'preuves',
        'conseils_securite',
        'conseils_lus',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'preuves' => 'array',
        'conseils_lus' => 'boolean',
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
}
