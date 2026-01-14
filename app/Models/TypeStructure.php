<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
