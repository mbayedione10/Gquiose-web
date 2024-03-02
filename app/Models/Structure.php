<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Structure extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'phone',
        'ville_id',
        'status',
        'adresse',
        'offre',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function typeStructure()
    {
        return $this->belongsTo(TypeStructure::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }
}
