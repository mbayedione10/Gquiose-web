<?php

namespace App\Filament\Resources\EvaluationStatsResource\Widgets;

use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class QuestionChartWidget extends ChartWidget
{
    public ?Model $record = null;

    protected static ?string $heading = 'Statistiques par question';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        if (!$this->record instanceof QuestionEvaluation) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $reponses = ReponseEvaluation::where('question_evaluation_id', $this->record->id)->get();

        if ($reponses->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label'           => 'Aucune réponse',
                        'data'            => [0],
                        'backgroundColor' => '#94a3b8',
                    ],
                ],
                'labels' => ['Pas de données'],
            ];
        }

        return match ($this->record->type) {
            'rating', 'scale'         => $this->getNumericChart($reponses),
            'yesno'                   => $this->getBinaryChart($reponses),
            'multiple_choice'         => $this->getMultipleChoiceChart($reponses),
            default                   => $this->getTextSummary($reponses),
        };
    }

    protected function getType(): string
    {
        return match ($this->record?->type) {
            'yesno', 'multiple_choice' => 'pie',
            default                    => 'bar',
        };
    }

    private function getNumericChart($reponses): array
    {
        $distribution = $reponses
            ->groupBy('valeur_numerique')
            ->map->count()
            ->sortKeys();

        return [
            'datasets' => [
                [
                    'label'           => 'Nombre de réponses',
                    'data'            => $distribution->values()->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor'     => '#059669',
                    'borderWidth'     => 1,
                ],
            ],
            'labels' => $distribution->keys()->map(fn ($v) => (string) $v)->toArray(),
        ];
    }

    private function getBinaryChart($reponses): array
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Réponses',
                    'data'            => $distribution->values()->toArray(),
                    'backgroundColor' => ['#10b981', '#ef4444'],
                ],
            ],
            'labels' => $distribution->keys()->map(fn ($k) => $k === 'yes' ? 'Oui' : 'Non')->toArray(),
        ];
    }

    private function getMultipleChoiceChart($reponses): array
    {
        $distribution = $reponses->groupBy('reponse')->map->count();

        $colors = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
            '#8b5cf6', '#ec4899', '#6366f1', '#14b8a6',
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Réponses',
                    'data'            => $distribution->values()->toArray(),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $distribution->keys()->toArray(),
        ];
    }

    private function getTextSummary($reponses): array
    {
        return [
            'datasets' => [
                [
                    'label'           => 'Réponses texte reçues',
                    'data'            => [$reponses->count()],
                    'backgroundColor' => '#6366f1',
                ],
            ],
            'labels' => ['Total : ' . $reponses->count()],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display'   => true,
                    'position'  => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}