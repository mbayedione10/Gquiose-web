<?php

namespace App\Filament\Resources\NotificationLogResource\Widgets;

use App\Models\NotificationLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NotificationLogsByCategoryWidget extends ChartWidget
{
    protected static ?string $heading = 'Notifications par catégorie (30 derniers jours)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $stats = NotificationLog::select(
            'category',
            DB::raw('COUNT(*) as count')
        )
            ->where('sent_at', '>=', now()->subDays(30))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        $labels = $stats->pluck('category')->map(function ($cat) {
            return ucfirst($cat ?? 'Autre');
        })->toArray();

        $data = $stats->pluck('count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Notifications',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',  // blue
                        'rgba(16, 185, 129, 0.5)',  // green
                        'rgba(245, 158, 11, 0.5)',  // amber
                        'rgba(239, 68, 68, 0.5)',   // red
                        'rgba(139, 92, 246, 0.5)',  // purple
                        'rgba(236, 72, 153, 0.5)',  // pink
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
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
        ];
    }
}
