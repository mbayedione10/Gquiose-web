<?php

namespace App\Filament\Widgets;
use App\Models\Response;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Support\Facades\DB;
class QuizBonneReponseChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Base de connaissances du Quiz par thématiques';
    protected static ?int $sort = 25;
    protected function getData(): array
    {
        $query = Response::query()
            ->join('questions', 'responses.question_id', 'questions.id')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select('thematiques.name as name', DB::raw('COUNT(responses.id) as data'))
            ->where('responses.isValid', 1)
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
