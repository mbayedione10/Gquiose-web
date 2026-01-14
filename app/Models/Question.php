<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'reponse',
        'option1',
        'option2',
        'option3',
        'option4',
        'status',
        'thematique_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function thematique()
    {
        return $this->belongsTo(Thematique::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
