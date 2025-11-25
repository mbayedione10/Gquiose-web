<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionEvaluation extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'question',
        'type',
        'options',
        'ordre',
        'obligatoire',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'options' => 'array',
        'obligatoire' => 'boolean',
        'status' => 'boolean',
    ];

    public function reponses()
    {
        return $this->hasMany(ReponseEvaluation::class);
    }
}
