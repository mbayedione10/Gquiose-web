<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class NotificationAnalyticsDashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Détecter quelle source utiliser
        $useNotificationLogs = NotificationLog::exists();

        // Statistiques des 7 derniers jours
        $last7Days = $useNotificationLogs 
            ? $this->getMetricsFromLogs(now()->subDays(7))
            : $this->getMetricsFromPush(now()->subDays(7));
        
        // Statistiques des 30 derniers jours
        $last30Days = $useNotificationLogs
            ? $this->getMetricsFromLogs(now()->subDays(30))
            : $this->getMetricsFromPush(now()->subDays(30));
        
        // Tendance (comparaison 7 derniers jours vs 7 jours précédents)
        $previous7Days = $useNotificationLogs
            ? $this->getMetricsFromLogs(now()->subDays(14), now()->subDays(7))
            : $this->getMetricsFromPush(now()->subDays(14), now()->subDays(7));
        
        $sentTrend = $this->calculateTrend($last7Days['sent_count'], $previous7Days['sent_count']);
        $openRateTrend = $this->calculateTrend($last7Days['open_rate'], $previous7Days['open_rate']);
        $clickRateTrend = $this->calculateTrend($last7Days['click_rate'], $previous7Days['click_rate']);

        return [
            Stat::make('Notifications envoyées (7j)', number_format($last7Days['sent_count']))
                ->description($this->getTrendDescription($sentTrend))
                ->descriptionIcon($sentTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($sentTrend >= 0 ? 'success' : 'danger')
                ->chart($useNotificationLogs ? $this->getLast7DaysChartFromLogs() : $this->getLast7DaysChartFromPush()),

            Stat::make('Taux d\'ouverture (7j)', number_format($last7Days['open_rate'], 1) . '%')
                ->description($this->getTrendDescription($openRateTrend, true))
                ->descriptionIcon($openRateTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($openRateTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Taux de clic (7j)', number_format($last7Days['click_rate'], 1) . '%')
                ->description($this->getTrendDescription($clickRateTrend, true))
                ->descriptionIcon($clickRateTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($clickRateTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Total livré (30j)', number_format($last30Days['delivered_count']))
                ->description('Sur ' . number_format($last30Days['sent_count']) . ' envoyées')
                ->color('info'),
        ];
    }

    /**
     * Récupérer les métriques depuis notification_logs (source précise)
     */
    private function getMetricsFromLogs($since, $until = null)
    {
        $query = NotificationLog::where('sent_at', '>=', $since);
        
        if ($until) {
            $query->where('sent_at', '<', $until);
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as sent_count'),
            DB::raw('SUM(CASE WHEN delivered_at IS NOT NULL THEN 1 ELSE 0 END) as delivered_count'),
            DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_count'),
            DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked_count')
        )->first();

        $sentCount = $stats->sent_count ?? 0;
        $deliveredCount = $stats->delivered_count ?? 0;
        $openedCount = $stats->opened_count ?? 0;
        $clickedCount = $stats->clicked_count ?? 0;

        return [
            'sent_count' => $sentCount,
            'delivered_count' => $deliveredCount,
            'opened_count' => $openedCount,
            'clicked_count' => $clickedCount,
            'delivery_rate' => $sentCount > 0 ? round(($deliveredCount / $sentCount) * 100, 2) : 0,
            'open_rate' => $deliveredCount > 0 ? round(($openedCount / $deliveredCount) * 100, 2) : 0,
            'click_rate' => $openedCount > 0 ? round(($clickedCount / $openedCount) * 100, 2) : 0,
        ];
    }

    /**
     * Récupérer les métriques depuis push_notifications (fallback)
     */
    private function getMetricsFromPush($since, $until = null)
    {
        $query = PushNotification::where('sent_at', '>=', $since)
            ->where('status', 'sent');
        
        if ($until) {
            $query->where('sent_at', '<', $until);
        }

        $stats = $query->select(
            DB::raw('SUM(sent_count) as sent_count'),
            DB::raw('SUM(delivered_count) as delivered_count'),
            DB::raw('SUM(opened_count) as opened_count'),
            DB::raw('SUM(clicked_count) as clicked_count')
        )->first();

        $sentCount = $stats->sent_count ?? 0;
        $deliveredCount = $stats->delivered_count ?? 0;
        $openedCount = $stats->opened_count ?? 0;
        $clickedCount = $stats->clicked_count ?? 0;

        return [
            'sent_count' => $sentCount,
            'delivered_count' => $deliveredCount,
            'opened_count' => $openedCount,
            'clicked_count' => $clickedCount,
            'delivery_rate' => $sentCount > 0 ? round(($deliveredCount / $sentCount) * 100, 2) : 0,
            'open_rate' => $deliveredCount > 0 ? round(($openedCount / $deliveredCount) * 100, 2) : 0,
            'click_rate' => $openedCount > 0 ? round(($clickedCount / $openedCount) * 100, 2) : 0,
        ];
    }

    /**
     * Calculer la tendance entre deux valeurs
     */
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Description de la tendance
     */
    private function getTrendDescription($trend, $isPercentage = false)
    {
        $prefix = $trend >= 0 ? '+' : '';
        $suffix = $isPercentage ? ' points' : '';
        
        return $prefix . number_format($trend, 1) . '%' . $suffix . ' vs période précédente';
    }

    /**
     * Graphique des 7 derniers jours (depuis logs)
     */
    private function getLast7DaysChartFromLogs(): array
    {
        $data = NotificationLog::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('sent_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        while (count($data) < 7) {
            array_unshift($data, 0);
        }

        return array_slice($data, -7);
    }

    /**
     * Graphique des 7 derniers jours (depuis push_notifications)
     */
    private function getLast7DaysChartFromPush(): array
    {
        $data = PushNotification::select(
            DB::raw('DATE(sent_at) as date'),
            DB::raw('SUM(sent_count) as count')
        )
            ->where('sent_at', '>=', now()->subDays(7))
            ->where('status', 'sent')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        while (count($data) < 7) {
            array_unshift($data, 0);
        }

        return array_slice($data, -7);
    }
}
