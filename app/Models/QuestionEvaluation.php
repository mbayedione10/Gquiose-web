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

    protected $attributes = [
        'options' => '[]',
    ];

    public function conditionQuestion()
    {
        return $this->belongsTo(QuestionEvaluation::class, 'condition_question_id');
    }

    /**
     * Add relationship to ReponseEvaluation in QuestionEvaluation model
     */
    public function reponsesEvaluations()
    {
        return $this->hasMany(ReponseEvaluation::class);
    }

    /**
     * Vérifier si la question doit être affichée selon la logique conditionnelle
     */
    public function shouldDisplay(array $previousResponses = []): bool
    {
        if (!$this->condition_question_id) {
            return true;
        }

        $conditionQuestion = self::find($this->condition_question_id);
        if (!$conditionQuestion) {
            return true;
        }

        $previousResponse = collect($previousResponses)
            ->firstWhere('question_evaluation_id', $this->condition_question_id);

        if (!$previousResponse) {
            return !$this->show_if_condition_met;
        }

        $responseValue = $previousResponse['reponse'] ?? $previousResponse['valeur_numerique'] ?? null;

        $conditionMet = match($this->condition_operator) {
            'equals' => $responseValue == $this->condition_value,
            'not_equals' => $responseValue != $this->condition_value,
            'greater_than' => $responseValue > $this->condition_value,
            'less_than' => $responseValue < $this->condition_value,
            default => true,
        };

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