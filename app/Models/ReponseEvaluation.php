<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReponseEvaluation extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'evaluation_id',
        'question_evaluation_id',
        'reponse',
        'valeur_numerique',
    ];

    protected $searchableFields = ['*'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function questionEvaluation()
    {
        return $this->belongsTo(QuestionEvaluation::class);
    }
}
