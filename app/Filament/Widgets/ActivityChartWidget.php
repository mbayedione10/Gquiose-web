<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use App\Models\Alerte;
use App\Models\Evaluation;
use App\Models\Response;
use App\Models\Article;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class ActivityChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Tendances d\'activité - 7 derniers jours';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('D d/m');
        });

        $utilisateurs = collect(range(6, 0))->map(function ($day) {
            return Utilisateur::whereDate('created_at', Carbon::now()->subDays($day))->count();
        });

        $alertes = collect(range(6, 0))->map(function ($day) {
            return Alerte::whereDate('created_at', Carbon::now()->subDays($day))->count();
        });

        $evaluations = collect(range(6, 0))->map(function ($day) {
            return Evaluation::whereDate('created_at', Carbon::now()->subDays($day))->count();
        });

        $quiz = collect(range(6, 0))->map(function ($day) {
            return Response::whereDate('created_at', Carbon::now()->subDays($day))->count();
        });

        $articles = collect(range(6, 0))->map(function ($day) {
            return Article::whereDate('created_at', Carbon::now()->subDays($day))->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux utilisateurs',
                    'data' => $utilisateurs->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Alertes VBG signalées',
                    'data' => $alertes->toArray(),
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Réponses au Quiz',
                    'data' => $quiz->toArray(),
                    'borderColor' => 'rgb(251, 146, 60)',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Évaluations app',
                    'data' => $evaluations->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Articles publiés',
                    'data' => $articles->toArray(),
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
