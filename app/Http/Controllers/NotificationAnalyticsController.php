<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationAnalyticsController extends Controller
{
    /**
     * Dashboard analytics complet
     * GET /api/v1/notifications/analytics/dashboard
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        try {
            $period = $request->input('period', '30'); // 7, 30, 90 jours

            $analytics = [
                'overview' => $this->getOverviewStats($period),
                'by_category' => $this->getStatsByCategory($period),
                'engagement_rates' => $this->getEngagementRates($period),
                'trends' => $this->getTrends($period),
                'top_performing' => $this->getTopPerforming(5),
                'delivery_stats' => $this->getDeliveryStats($period),
            ];

            return response()->json([
                'success' => true,
                'period' => $period . ' jours',
                'data' => $analytics,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching notification analytics', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Statistiques globales
     */
    protected function getOverviewStats($period)
    {
        $startDate = now()->subDays($period);

        return [
            'total_sent' => PushNotification::where('status', 'sent')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'total_delivered' => NotificationLog::where('status', 'sent')
                ->where('sent_at', '>=', $startDate)
                ->count(),
            'total_opened' => NotificationLog::whereNotNull('opened_at')
                ->where('sent_at', '>=', $startDate)
                ->count(),
            'total_clicked' => NotificationLog::whereNotNull('clicked_at')
                ->where('sent_at', '>=', $startDate)
                ->count(),
            'unique_recipients' => NotificationLog::where('sent_at', '>=', $startDate)
                ->distinct('utilisateur_id')
                ->count('utilisateur_id'),
        ];
    }

    /**
     * Statistiques par catégorie
     */
    protected function getStatsByCategory($period)
    {
        $startDate = now()->subDays($period);

        return NotificationLog::select(
            'category',
            DB::raw('COUNT(*) as total_sent'),
            DB::raw('COUNT(CASE WHEN opened_at IS NOT NULL THEN 1 END) as total_opened'),
            DB::raw('COUNT(CASE WHEN clicked_at IS NOT NULL THEN 1 END) as total_clicked'),
            DB::raw('ROUND(COUNT(CASE WHEN opened_at IS NOT NULL THEN 1 END) * 100.0 / COUNT(*), 2) as open_rate'),
            DB::raw('ROUND(COUNT(CASE WHEN clicked_at IS NOT NULL THEN 1 END) * 100.0 / COUNT(*), 2) as click_rate')
        )
            ->where('sent_at', '>=', $startDate)
            ->groupBy('category')
            ->get();
    }

    /**
     * Taux d'engagement globaux
     */
    protected function getEngagementRates($period)
    {
        $startDate = now()->subDays($period);

        $stats = NotificationLog::where('sent_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN opened_at IS NOT NULL THEN 1 END) as opened,
                COUNT(CASE WHEN clicked_at IS NOT NULL THEN 1 END) as clicked
            ')
            ->first();

        $total = $stats->total ?: 1; // Éviter division par zéro

        return [
            'delivery_rate' => 100, // Supposons 100% de livraison si status=sent
            'open_rate' => round(($stats->opened / $total) * 100, 2),
            'click_rate' => round(($stats->clicked / $total) * 100, 2),
            'click_to_open_rate' => $stats->opened > 0 
                ? round(($stats->clicked / $stats->opened) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Tendances quotidiennes
     */
    protected function getTrends($period)
    {
        $startDate = now()->subDays($period);

        return NotificationLog::where('sent_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(sent_at) as date'),
                DB::raw('COUNT(*) as sent'),
                DB::raw('COUNT(CASE WHEN opened_at IS NOT NULL THEN 1 END) as opened'),
                DB::raw('COUNT(CASE WHEN clicked_at IS NOT NULL THEN 1 END) as clicked')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Top notifications les plus performantes
     */
    protected function getTopPerforming($limit = 5)
    {
        return PushNotification::select(
            'push_notifications.id',
            'push_notifications.title',
            'push_notifications.category',
            DB::raw('COUNT(notification_logs.id) as total_sent'),
            DB::raw('COUNT(CASE WHEN notification_logs.opened_at IS NOT NULL THEN 1 END) as total_opened'),
            DB::raw('COUNT(CASE WHEN notification_logs.clicked_at IS NOT NULL THEN 1 END) as total_clicked'),
            DB::raw('ROUND(COUNT(CASE WHEN notification_logs.opened_at IS NOT NULL THEN 1 END) * 100.0 / COUNT(notification_logs.id), 2) as open_rate')
        )
            ->join('notification_logs', 'push_notifications.id', '=', 'notification_logs.notification_schedule_id')
            ->where('push_notifications.status', 'sent')
            ->groupBy('push_notifications.id', 'push_notifications.title', 'push_notifications.category')
            ->orderBy('open_rate', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Statistiques de livraison
     */
    protected function getDeliveryStats($period)
    {
        $startDate = now()->subDays($period);

        return [
            'by_platform' => NotificationLog::where('sent_at', '>=', $startDate)
                ->select(
                    'platform',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('COUNT(CASE WHEN status = "sent" THEN 1 END) as delivered'),
                    DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed')
                )
                ->groupBy('platform')
                ->get(),
            
            'failure_reasons' => NotificationLog::where('sent_at', '>=', $startDate)
                ->where('status', 'failed')
                ->select('error_message', DB::raw('COUNT(*) as count'))
                ->groupBy('error_message')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Analyse de cohorte utilisateur
     * GET /api/v1/notifications/analytics/cohort
     */
    public function cohortAnalysis(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        try {
            // Analyser l'engagement par cohorte d'inscription
            $cohorts = Utilisateur::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as cohort'),
                DB::raw('COUNT(*) as users_count'),
                DB::raw('COUNT(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 END) as push_enabled'),
                DB::raw('ROUND(COUNT(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 END) * 100.0 / COUNT(*), 2) as push_enabled_rate')
            )
                ->groupBy('cohort')
                ->orderBy('cohort', 'desc')
                ->limit(12) // 12 derniers mois
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cohorts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching cohort analysis', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * Export des analytics en CSV
     * GET /api/v1/notifications/analytics/export
     */
    public function exportAnalytics(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        // TODO: Implémenter l'export CSV
        return response()->json([
            'success' => false,
            'message' => 'Export non encore implémenté',
        ], 501);
    }
}
