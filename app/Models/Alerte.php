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
    ];

    protected $searchableFields = ['*'];

    public function typeAlerte()
    {
        return $this->belongsTo(TypeAlerte::class);
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }
}
