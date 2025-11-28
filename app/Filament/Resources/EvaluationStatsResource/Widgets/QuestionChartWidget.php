<?php

namespace App\Filament\Resources\EvaluationStatsResource\Widgets;

use App\Models\ReponseEvaluation;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class QuestionChartWidget extends ChartWidget
{
    public ?Model $record = null;

    protected function getData(): array
    {
        if (!$this->record) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $reponses = ReponseEvaluation::where('question_evaluation_id', $this->record->id)->get();

        if ($reponses->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Aucune donnée',
                        'data' => [0],
                    ],
                ],
                'labels' => ['Pas de réponses'],
            ];
        }

        switch ($this->record->type) {
            case 'rating':
            case 'scale':
                return $this->getNumericChart($reponses);
            
            case 'yesno':
                return $this->getBinaryChart($reponses);
            
            case 'multiple_choice':
                return $this->getMultipleChoiceChart($reponses);
            
            default:
                return $this->getTextSummary($reponses);
        }
    }

    protected function getType(): string
    {
        if (!$this->record) {
            return 'bar';
        }

        return match($this->record->type) {
            'yesno', 'multiple_choice' => 'pie',
            'rating', 'scale' => 'bar',
            default => 'bar',
        };
    }

    protected function getNumericChart($reponses)
    {
        $distribution = $reponses->groupBy('valeur_numerique')
            ->map->count()
            ->sortKeys();

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de réponses',
                    'data' => $distribution->values()->toArray(),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $distribution->keys()->map(fn($v) => (string)$v)->toArray(),
        ];
    }

    protected function getBinaryChart($reponses)
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        return [
            'datasets' => [
                [
                    'label' => 'Réponses',
                    'data' => $distribution->values()->toArray(),
                    'backgroundColor' => ['#10b981', '#ef4444'],
                ],
            ],
            'labels' => $distribution->keys()->toArray(),
        ];
    }

    protected function getMultipleChoiceChart($reponses)
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        return [
            'datasets' => [
                [
                    'label' => 'Choix',
                    'data' => $distribution->values()->toArray(),
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                ],
            ],
            'labels' => $distribution->keys()->toArray(),
        ];
    }

    protected function getTextSummary($reponses)
    {
        return [
            'datasets' => [
                [
                    'label' => 'Réponses texte',
                    'data' => [$reponses->count()],
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => ['Total'],
        ];
    }

    protected function getHeading(): ?string
    {
        return $this->record ? 'Graphique : ' . $this->record->question : 'Statistiques';
    }
}
