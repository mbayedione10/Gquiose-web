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
        'formulaire_type',
        'options',
        'ordre',
        'obligatoire',
        'status',
        'condition_question_id',
        'condition_operator',
        'condition_value',
        'show_if_condition_met',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'options' => 'array',
        'obligatoire' => 'boolean',
        'status' => 'boolean',
        'show_if_condition_met' => 'boolean',
    ];

    public function conditionQuestion()
    {
        return $this->belongsTo(QuestionEvaluation::class, 'condition_question_id');
    }

    /**
     * Vérifier si la question doit être affichée selon la logique conditionnelle
     */
    public function shouldDisplay(array $previousAnswers)
    {
        if (!$this->condition_question_id) {
            return true;
        }

        $conditionAnswer = $previousAnswers[$this->condition_question_id] ?? null;
        
        if ($conditionAnswer === null) {
            return false;
        }

        $conditionMet = $this->evaluateCondition($conditionAnswer);
        
        return $this->show_if_condition_met ? $conditionMet : !$conditionMet;
    }

    private function evaluateCondition($answer)
    {
        switch ($this->condition_operator) {
            case 'equals':
                return $answer == $this->condition_value;
            case 'not_equals':
                return $answer != $this->condition_value;
            case 'greater_than':
                return $answer > $this->condition_value;
            case 'less_than':
                return $answer < $this->condition_value;
            case 'contains':
                return strpos($answer, $this->condition_value) !== false;
            default:
                return true;
        }
    }

    public function reponses()
    {
        return $this->hasMany(ReponseEvaluation::class);
    }
}
