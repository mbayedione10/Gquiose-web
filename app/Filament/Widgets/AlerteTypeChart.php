<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class AlerteTypeChart extends BarChartWidget
{
    protected static ?string $heading = 'Alertes par type de violence';

    protected static ?int $sort = 10;
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $alertesParType = Alerte::query()
            ->join('type_alertes', 'alertes.type_alerte_id', '=', 'type_alertes.id')
            ->select('type_alertes.name', DB::raw('COUNT(alertes.id) as total'))
            ->groupBy('type_alertes.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $labels = $alertesParType->pluck('name')->toArray();
        $data = $alertesParType->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Nombre d\'alertes',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(251, 146, 60)',
                        'rgb(245, 158, 11)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)',
                        'rgb(16, 185, 129)',
                        'rgb(20, 184, 166)',
                        'rgb(14, 165, 233)',
                        'rgb(59, 130, 246)',
                        'rgb(99, 102, 241)',
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
