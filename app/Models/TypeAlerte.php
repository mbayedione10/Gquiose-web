<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAlerte extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'status'];

    protected $searchableFields = ['*'];

    protected $table = 'type_alertes';

    protected $casts = [
        'status' => 'boolean',
    ];

    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }

    /**
     * Relation avec la catégorie de conseils
     */
    public function categorieConseil()
    {
        return $this->hasOne(CategorieConseil::class, 'type_alerte_id');
    }
}
