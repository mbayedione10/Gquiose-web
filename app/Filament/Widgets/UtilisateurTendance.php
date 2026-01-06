<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class UtilisateurTendance extends LineChartWidget
{
    protected static ?string $heading = 'Croissance des utilisateurs - Année en cours';

    protected int | string | array $columnSpan = "full";

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $utilisateurs = Utilisateur::whereYear('created_at', $currentYear)->get();

        $dataByMonth = $utilisateurs->groupBy(function ($user) {
            return Carbon::parse($user->created_at)->month;
        })->map->count();

        $labels = [];
        $nouveauxUtilisateurs = [];
        $totalCumule = [];
        $cumul = 0;

        for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
            // Ne montrer que jusqu'au mois en cours
            if ($monthNum > $currentMonth) {
                break;
            }
            
            $labels[] = $months[$monthNum];
            $count = $dataByMonth->get($monthNum, 0);
            $nouveauxUtilisateurs[] = $count;
            $cumul += $count;
            $totalCumule[] = $cumul;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux utilisateurs',
                    'data' => $nouveauxUtilisateurs,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Total cumulé',
                    'data' => $totalCumule,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => false,
                    'borderDash' => [5, 5],
                    'tension' => 0.4,
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
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
