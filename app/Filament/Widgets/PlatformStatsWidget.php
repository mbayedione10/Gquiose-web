<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalUsers = Utilisateur::count();
        $iosUsers = Utilisateur::where('platform', 'ios')->count();
        $androidUsers = Utilisateur::where('platform', 'android')->count();
        $webUsers = Utilisateur::whereNull('platform')->orWhere('platform', '')->count();

        // Stats temporelles
        $iosUsers7j = Utilisateur::where('platform', 'ios')->where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $androidUsers7j = Utilisateur::where('platform', 'android')->where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            Stat::make('iOS', number_format($iosUsers))
                ->description(number_format(($totalUsers > 0 ? ($iosUsers / $totalUsers) * 100 : 0), 1).'% • +'.$iosUsers7j.' cette semaine')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('info')
                ->chart($this->getPlatformChart('ios')),

            Stat::make('Android', number_format($androidUsers))
                ->description(number_format(($totalUsers > 0 ? ($androidUsers / $totalUsers) * 100 : 0), 1).'% • +'.$androidUsers7j.' cette semaine')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('success')
                ->chart($this->getPlatformChart('android')),

            Stat::make('Web/Autres', number_format($webUsers))
                ->description(number_format(($totalUsers > 0 ? ($webUsers / $totalUsers) * 100 : 0), 1).'% du total')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('gray')
                ->chart($this->getPlatformChart(null)),
        ];
    }

    private function getPlatformChart(?string $platform): array
    {
        return collect(range(6, 0))->map(function ($day) use ($platform) {
            $query = Utilisateur::whereDate('created_at', Carbon::now()->subDays($day));

            if ($platform) {
                $query->where('platform', $platform);
            } else {
                $query->where(function ($q) {
                    $q->whereNull('platform')->orWhere('platform', '');
                });
            }

            return $query->count();
        })->toArray();
    }
}
