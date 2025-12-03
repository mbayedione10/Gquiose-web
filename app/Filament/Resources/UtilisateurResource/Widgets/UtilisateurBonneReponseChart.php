<?php

namespace App\Filament\Resources\UtilisateurResource\Widgets;
use App\Models\Response;
use App\Models\Utilisateur;
use Filament\Widgets\DoughnutChartWidget;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
class UtilisateurBonneReponseChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Base de connaissances du Quiz par thématique';
    public ?Utilisateur $record;
    protected function getData(): array
    {
        $query = Response::query()
            ->join('questions', 'responses.question_id', 'questions.id')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select('thematiques.name as name', DB::raw('COUNT(responses.id) as data'))
            ->where('responses.utilisateur_id', $this->record->id)
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
