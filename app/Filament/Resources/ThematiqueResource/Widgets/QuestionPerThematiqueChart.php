<?php

namespace App\Filament\Resources\ThematiqueChartResource\Widgets;

use App\Models\Question;
use App\Models\Response;
use App\Models\Thematique;
use Filament\Widgets\BubbleChartWidget;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Support\Facades\DB;

class QuestionPerThematiqueChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Nombre de bonnes réponses par thématiques';

    protected string|int|array $columnSpan = 1;

    protected static ?int $sort = 11;


    protected function getData(): array
    {
        $query = Response::query()
            ->join('questions', 'responses.question_id', 'questions.id')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select('thematiques.name as name', DB::raw('COUNT(*) as data'))
            ->where('isValid', true)
            ->groupBy('name');

        $labels = $query->pluck('name')->toArray();
        $data = $query->pluck('data')->toArray();




        return [
            'datasets' => [
                [
                    'label' => "Questions",
                    'data' => $data
                ],
            ],
            'labels' => $labels
        ];
    }
}
