<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeStructure extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'icon', 'status'];

    protected $searchableFields = ['*'];

    protected $table = 'type_structures';

    protected $casts = [
        'status' => 'boolean',
    ];

    public function structures()
    {
        return $this->hasMany(Structure::class);
    }
}
