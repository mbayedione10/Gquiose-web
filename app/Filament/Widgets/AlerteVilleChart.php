<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use Filament\Widgets\DoughnutChartWidget;
use Illuminate\Support\Facades\DB;

class AlerteVilleChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Répartition géographique des alertes';

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $alertesParVille = Alerte::query()
            ->join('villes', 'alertes.ville_id', '=', 'villes.id')
            ->select('villes.name', DB::raw('COUNT(alertes.id) as total'))
            ->groupBy('villes.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $labels = $alertesParVille->pluck('name')->toArray();
        $data = $alertesParVille->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Alertes par ville',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(251, 146, 60)',
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                        'rgb(20, 184, 166)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
        ];
    }
}
