<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\NotificationLog;
use App\Models\Utilisateur;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class CohortAnalysisWidget extends Widget
{
    protected static string $view = 'filament.resources.push-notification-resource.widgets.cohort-analysis-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getCohortData(): array
    {
        // Récupérer les données de cohorte
        $cohorts = DB::table('utilisateurs')
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as cohort"),
                DB::raw('COUNT(*) as users_count'),
            ])
            ->where('status', true)
            ->whereNotNull('onesignal_player_id')
            ->groupBy('cohort')
            ->orderByDesc('cohort')
            ->limit(12) // 12 derniers mois
            ->get();

        $results = [];

        foreach ($cohorts as $cohort) {
            // Récupérer les IDs utilisateurs de cette cohorte
            $userIds = Utilisateur::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$cohort->cohort])
                ->where('status', true)
                ->whereNotNull('onesignal_player_id')
                ->pluck('id');

            if ($userIds->isEmpty()) {
                continue;
            }

            // Statistiques de notifications pour cette cohorte
            $stats = NotificationLog::whereIn('utilisateur_id', $userIds)
                ->select([
                    DB::raw('COUNT(*) as total_received'),
                    DB::raw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as total_opened'),
                    DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as total_clicked'),
                ])
                ->first();

            $totalReceived = $stats->total_received ?? 0;
            $totalOpened = $stats->total_opened ?? 0;
            $totalClicked = $stats->total_clicked ?? 0;

            $openRate = $totalReceived > 0 ? round(($totalOpened / $totalReceived) * 100, 1) : 0;
            $clickRate = $totalOpened > 0 ? round(($totalClicked / $totalOpened) * 100, 1) : 0;
            $avgPerUser = $cohort->users_count > 0 ? round($totalReceived / $cohort->users_count, 1) : 0;

            // Score d'engagement (formule: (open_rate * 0.6) + (click_rate * 0.4))
            $engagementScore = ($openRate * 0.6) + ($clickRate * 0.4);

            // Formater le nom de la cohorte
            $cohortDate = \Carbon\Carbon::createFromFormat('Y-m', $cohort->cohort);
            $cohortName = $cohortDate->translatedFormat('F Y');

            $results[] = [
                'cohort' => $cohortName,
                'users_count' => number_format($cohort->users_count),
                'notifications_received' => number_format($totalReceived),
                'avg_per_user' => $avgPerUser,
                'open_rate' => $openRate,
                'click_rate' => $clickRate,
                'engagement_score' => $engagementScore,
            ];
        }

        return $results;
    }
}
