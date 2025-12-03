<?php

namespace App\Filament\Widgets;
use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $totalUsers = Utilisateur::count();
        $iosUsers = Utilisateur::where('platform', 'ios')->count();
        $androidUsers = Utilisateur::where('platform', 'android')->count();
        return [
            Stat::make('Nombre d\'utilisateurs', $totalUsers)
                ->description(Utilisateur::where('status', true)->count() . ' actifs')
                ->descriptionIcon('heroicon-s-users')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $totalUsers]),
            Stat::make('Plateforme iOS', $iosUsers)
                ->description(number_format(($totalUsers > 0 ? ($iosUsers / $totalUsers) * 100 : 0), 1) . '% du total')
                ->descriptionIcon('heroicon-s-device-phone-mobile')
                ->color('info')
                ->chart([3, 5, 7, 9, 11, 13, $iosUsers]),
            Stat::make('Plateforme Android', $androidUsers)
                ->description(number_format(($totalUsers > 0 ? ($androidUsers / $totalUsers) * 100 : 0), 1) . '% du total')
                ->descriptionIcon('heroicon-s-device-phone-mobile')
                ->color('warning')
                ->chart([4, 7, 8, 9, 11, 12, $androidUsers]),
        ];
    }
}
