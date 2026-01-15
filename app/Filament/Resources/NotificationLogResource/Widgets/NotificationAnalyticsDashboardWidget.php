<?php

namespace App\Filament\Resources\NotificationLogResource\Widgets;

use App\Models\NotificationLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class NotificationAnalyticsDashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Statistiques des 7 derniers jours
        $last7Days = $this->getMetrics(now()->subDays(7));
        
        // Statistiques des 30 derniers jours
        $last30Days = $this->getMetrics(now()->subDays(30));
        
        // Tendance (comparaison 7 derniers jours vs 7 jours précédents)
        $previous7Days = $this->getMetrics(now()->subDays(14), now()->subDays(7));
        
        $sentTrend = $this->calculateTrend($last7Days['sent_count'], $previous7Days['sent_count']);
        $openRateTrend = $this->calculateTrend($last7Days['open_rate'], $previous7Days['open_rate']);
        $clickRateTrend = $this->calculateTrend($last7Days['click_rate'], $previous7Days['click_rate']);

        return [
            Stat::make('Notifications envoyées (7j)', number_format($last7Days['sent_count']))
                ->description($this->getTrendDescription($sentTrend))
                ->descriptionIcon($sentTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($sentTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getLast7DaysChart()),

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
     * Récupérer les métriques pour une période
     */
    private function getMetrics($since, $until = null)
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
     * Graphique des 7 derniers jours
     */
    private function getLast7DaysChart(): array
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

        // Remplir les jours manquants avec 0
        while (count($data) < 7) {
            array_unshift($data, 0);
        }

        return array_slice($data, -7);
    }
}
