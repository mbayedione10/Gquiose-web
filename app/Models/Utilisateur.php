<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['nom', 'prenom', 'email', 'phone', 'sexe', 'status'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
