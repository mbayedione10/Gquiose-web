<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class UtilisateurTendance extends LineChartWidget
{
    protected static ?string $heading = 'Évolution des utilisateurs';

    protected int | string | array $columnSpan = "full";

    protected static ?int $sort = 10;

    protected function getData(): array
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $currentYear = Carbon::now()->year;

        $utilisateurs = Utilisateur::whereYear('created_at', $currentYear)->get();

        $dataByMonth = $utilisateurs->groupBy(function ($user) {
            return Carbon::parse($user->created_at)->month;
        })->map->count();

        $labels = [];
        $data = [];

        foreach ($months as $monthNum => $monthName) {
            $labels[] = $monthName;
            $data[] = $dataByMonth->get($monthNum, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Nouveaux utilisateurs',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

}
