<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\NotificationLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class NotificationAnalyticsDashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Période de 7 jours
        $last7Days = now()->subDays(7);
        
        // Statistiques 7 derniers jours
        $stats7d = NotificationLog::where('created_at', '>=', $last7Days)
            ->select([
                DB::raw('COUNT(*) as total_sent'),
                DB::raw('SUM(CASE WHEN delivered_at IS NOT NULL THEN 1 ELSE 0 END) as total_delivered'),
                DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as total_opened'),
                DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as total_clicked'),
            ])
            ->first();

        $sentCount7d = $stats7d->total_sent ?? 0;
        $deliveredCount7d = $stats7d->total_delivered ?? 0;
        $openedCount7d = $stats7d->total_opened ?? 0;
        $clickedCount7d = $stats7d->total_clicked ?? 0;

        // Calcul des taux
        $deliveryRate = $sentCount7d > 0 ? round(($deliveredCount7d / $sentCount7d) * 100, 1) : 0;
        $openRate = $deliveredCount7d > 0 ? round(($openedCount7d / $deliveredCount7d) * 100, 1) : 0;
        $clickRate = $openedCount7d > 0 ? round(($clickedCount7d / $openedCount7d) * 100, 1) : 0;

        // Tendances (comparaison avec 7 jours précédents)
        $previous7Days = now()->subDays(14);
        $statsPrev = NotificationLog::whereBetween('created_at', [$previous7Days, $last7Days])
            ->select([
                DB::raw('COUNT(*) as total_sent'),
                DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as total_opened'),
            ])
            ->first();

        $sentTrend = $sentCount7d - ($statsPrev->total_sent ?? 0);
        $openedTrend = $openedCount7d - ($statsPrev->total_opened ?? 0);

        // Catégorie la plus utilisée (category existe dans notification_logs)
        $topCategory = NotificationLog::where('created_at', '>=', $last7Days)
            ->whereNotNull('category')
            ->select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('count')
            ->first();

        return [
            Stat::make('Notifications envoyées (7j)', number_format($sentCount7d))
                ->description($sentTrend >= 0 ? '+'.$sentTrend.' vs semaine précédente' : $sentTrend.' vs semaine précédente')
                ->descriptionIcon($sentTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($sentTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getSentTrend()),

            Stat::make('Taux de livraison', $deliveryRate.'%')
                ->description($deliveredCount7d.' livrées sur '.$sentCount7d.' envoyées')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($deliveryRate >= 95 ? 'success' : ($deliveryRate >= 85 ? 'warning' : 'danger')),

            Stat::make('Taux d\'ouverture', $openRate.'%')
                ->description($openedCount7d.' ouvertures')
                ->descriptionIcon($openedTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($openRate >= 40 ? 'success' : ($openRate >= 20 ? 'warning' : 'danger'))
                ->chart($this->getOpenTrend()),

            Stat::make('Taux de clic', $clickRate.'%')
                ->description($clickedCount7d.' clics')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color($clickRate >= 30 ? 'success' : ($clickRate >= 15 ? 'warning' : 'danger')),

            Stat::make('Catégorie la plus performante', ucfirst($topCategory->category ?? 'N/A'))
                ->description(($topCategory->count ?? 0).' notifications')
                ->descriptionIcon('heroicon-m-star')
                ->color('info'),
        ];
    }

    /**
     * Graphique tendance des envois (7 derniers jours)
     */
    protected function getSentTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = NotificationLog::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Graphique tendance des ouvertures (7 derniers jours)
     */
    protected function getOpenTrend(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = NotificationLog::whereDate('opened_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
