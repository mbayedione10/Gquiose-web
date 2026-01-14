<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plateforme extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'nom',
        'description',
        'signalement_url',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];
}
