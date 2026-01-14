<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\DoughnutChartWidget;

class AgeRangeStatsWidget extends DoughnutChartWidget
{
    protected static ?string $heading = 'Répartition par tranche d\'âge';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentYear = now()->year;

        $tranches = [
            'moins_15' => [
                'color' => 'rgba(239, 68, 68, 0.8)',
                'label' => 'Moins de 15 ans',
                'min_age' => 0,
                'max_age' => 14,
                'dob_value' => '-15 ans',
            ],
            '15_17' => [
                'color' => 'rgba(249, 115, 22, 0.8)',
                'label' => '15-17 ans',
                'min_age' => 15,
                'max_age' => 17,
                'dob_value' => '15-17 ans',
            ],
            '18_24' => [
                'color' => 'rgba(234, 179, 8, 0.8)',
                'label' => '18-24 ans',
                'min_age' => 18,
                'max_age' => 24,
                'dob_value' => '18-24 ans',
            ],
            '25_29' => [
                'color' => 'rgba(34, 197, 94, 0.8)',
                'label' => '25-29 ans',
                'min_age' => 25,
                'max_age' => 29,
                'dob_value' => '25-29 ans',
            ],
            '30_35' => [
                'color' => 'rgba(59, 130, 246, 0.8)',
                'label' => '30-35 ans',
                'min_age' => 30,
                'max_age' => 35,
                'dob_value' => '30-35 ans',
            ],
            'plus_35' => [
                'color' => 'rgba(139, 92, 246, 0.8)',
                'label' => 'Plus de 35 ans',
                'min_age' => 36,
                'max_age' => 200,
                'dob_value' => '+35 ans',
            ],
        ];

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($tranches as $key => $config) {
            $minYear = $currentYear - $config['max_age'];
            $maxYear = $currentYear - $config['min_age'];

            // Calcul dynamique via anneedenaissance
            $countDynamic = Utilisateur::whereNotNull('anneedenaissance')
                ->where('anneedenaissance', '>', 0)
                ->whereBetween('anneedenaissance', [$minYear, $maxYear])
                ->count();

            // Fallback sur dob pour ceux sans anneedenaissance
            $countFallback = Utilisateur::where(function ($query) {
                $query->whereNull('anneedenaissance')
                    ->orWhere('anneedenaissance', 0);
            })
                ->where('dob', $config['dob_value'])
                ->count();

            $count = $countDynamic + $countFallback;
            $data[] = $count;
            $labels[] = $config['label'].' ('.$count.')';
            $colors[] = $config['color'];
        }

        // Utilisateurs sans aucune info d'âge
        $sansAge = Utilisateur::where(function ($query) {
            $query->whereNull('anneedenaissance')
                ->orWhere('anneedenaissance', 0);
        })
            ->where(function ($query) {
                $query->whereNull('dob')
                    ->orWhere('dob', '');
            })
            ->count();

        if ($sansAge > 0) {
            $data[] = $sansAge;
            $labels[] = 'Non renseigné ('.$sansAge.')';
            $colors[] = 'rgba(156, 163, 175, 0.8)';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Utilisateurs',
                    'data' => $data,
                    'backgroundColor' => $colors,
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
                    'position' => 'right',
                ],
            ],
        ];
    }
}
