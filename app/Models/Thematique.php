<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Thematique extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'status'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
