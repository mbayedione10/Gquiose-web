<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NotificationsByCategoryWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des notifications (30 derniers jours)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Détecter quelle source utiliser
        $useNotificationLogs = NotificationLog::exists();

        if ($useNotificationLogs) {
            return $this->getDataFromLogs();
        }

        return $this->getDataFromPush();
    }

    /**
     * Données depuis notification_logs (par catégorie)
     */
    private function getDataFromLogs(): array
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
            return match($cat) {
                'article' => 'Article',
                'forum' => 'Forum',
                'admin' => 'Admin',
                'evaluation' => 'Évaluation',
                'cycle' => 'Cycle',
                'content' => 'Contenu',
                default => ucfirst($cat ?? 'Autre')
            };
        })->toArray();

        $data = $stats->pluck('count')->toArray();

        return $this->formatChartData($labels, $data);
    }

    /**
     * Données depuis push_notifications (par type)
     */
    private function getDataFromPush(): array
    {
        $stats = PushNotification::select(
            'type',
            DB::raw('SUM(sent_count) as total_sent')
        )
            ->where('sent_at', '>=', now()->subDays(30))
            ->where('status', 'sent')
            ->whereNotNull('type')
            ->groupBy('type')
            ->orderByDesc('total_sent')
            ->get();

        $labels = $stats->pluck('type')->map(function ($type) {
            return match($type) {
                'manual' => 'Manuel',
                'automatic' => 'Automatique',
                'scheduled' => 'Programmé',
                default => ucfirst($type)
            };
        })->toArray();

        $data = $stats->pluck('total_sent')->toArray();

        return $this->formatChartData($labels, $data);
    }

    /**
     * Formater les données du graphique
     */
    private function formatChartData(array $labels, array $data): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Notifications',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(16, 185, 129, 0.5)',
                        'rgba(245, 158, 11, 0.5)',
                        'rgba(239, 68, 68, 0.5)',
                        'rgba(139, 92, 246, 0.5)',
                        'rgba(236, 72, 153, 0.5)',
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
