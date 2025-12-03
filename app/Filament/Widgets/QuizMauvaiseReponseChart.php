<?php

namespace App\Filament\Widgets;
use App\Models\Response;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Support\Facades\DB;
class QuizMauvaiseReponseChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Base de connaissances des mauvaises réponses par thématique';
    protected static ?int $sort = 20;
    protected function getData(): array
    {
        $query = Response::query()
            ->join('questions', 'responses.question_id', 'questions.id')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select('thematiques.name as name', DB::raw('COUNT(responses.id) as data'))
            ->where('responses.isValid', 0)
            ->groupBy('name');
        $labels = $query->pluck('name')->toArray();
        $data = $query->pluck('data')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Thématique',
                    'data' => $data,
                    'backgroundColor' => chartColors()
                ],
            ],
            'labels' => $labels,
        ];
    }
}
