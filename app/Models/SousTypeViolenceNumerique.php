<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SousTypeViolenceNumerique extends Model
{
    use HasFactory;
    use Searchable;

    protected $table = 'sous_types_violence_numerique';

    protected $fillable = [
        'nom',
        'description',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function alertes()
    {
        return $this->hasMany(Alerte::class, 'sous_type_violence_numerique_id');
    }
}
