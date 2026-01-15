<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationsTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Tendance des notifications (30 derniers jours)';

    protected static ?int $sort = 3;

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
     * Données depuis notification_logs (tracking précis)
     */
    private function getDataFromLogs(): array
    {
        $endDate = now();
        $startDate = now()->subDays(30);

        $allDates = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $allDates[$date->format('Y-m-d')] = [
                'sent' => 0,
                'opened' => 0,
                'clicked' => 0,
            ];
        }

        $stats = NotificationLog::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('COUNT(*) as sent_count'),
            DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_count'),
            DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_count')
        )
            ->where('sent_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($stats as $stat) {
            if (isset($allDates[$stat->date])) {
                $allDates[$stat->date] = [
                    'sent' => $stat->sent_count,
                    'opened' => $stat->opened_count,
                    'clicked' => $stat->clicked_count,
                ];
            }
        }

        return $this->formatChartData($allDates);
    }

    /**
     * Données depuis push_notifications (compteurs agrégés)
     */
    private function getDataFromPush(): array
    {
        $endDate = now();
        $startDate = now()->subDays(30);

        $allDates = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $allDates[$date->format('Y-m-d')] = [
                'sent' => 0,
                'opened' => 0,
                'clicked' => 0,
            ];
        }

        $stats = PushNotification::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('SUM(sent_count) as sent_count'),
            DB::raw('SUM(opened_count) as opened_count'),
            DB::raw('SUM(clicked_count) as clicked_count')
        )
            ->where('sent_at', '>=', $startDate)
            ->where('status', 'sent')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($stats as $stat) {
            if (isset($allDates[$stat->date])) {
                $allDates[$stat->date] = [
                    'sent' => $stat->sent_count,
                    'opened' => $stat->opened_count,
                    'clicked' => $stat->clicked_count,
                ];
            }
        }

        return $this->formatChartData($allDates);
    }

    /**
     * Formater les données du graphique
     */
    private function formatChartData(array $allDates): array
    {
        $labels = array_keys($allDates);
        $labels = array_map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        }, $labels);

        $sentData = array_column($allDates, 'sent');
        $openedData = array_column($allDates, 'opened');
        $clickedData = array_column($allDates, 'clicked');

        return [
            'datasets' => [
                [
                    'label' => 'Envoyées',
                    'data' => $sentData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Ouvertes',
                    'data' => $openedData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Cliquées',
                    'data' => $clickedData,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
