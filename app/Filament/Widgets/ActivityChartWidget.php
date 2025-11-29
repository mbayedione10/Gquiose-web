
<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use App\Models\Alerte;
use App\Models\Evaluation;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class ActivityChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Activité des 7 derniers jours';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('d/m');
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

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux utilisateurs',
                    'data' => $utilisateurs->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Alertes',
                    'data' => $alertes->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
                [
                    'label' => 'Évaluations',
                    'data' => $evaluations->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }
}
