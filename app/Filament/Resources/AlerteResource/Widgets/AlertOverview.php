<?php

namespace App\Filament\Resources\AlerteResource\Widgets;

use App\Models\Alerte;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Console\View\Components\Alert;

class AlertOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Alertes", Alerte::count())
                ->description("Totale des alertes signalées"),

            Stat::make("Confirmées", Alerte::where('etat', 'Confirmée')->count())
                ->descriptionColor('success')
                ->description("Totale des alertes confirmées"),

            Stat::make("Non confirmées", Alerte::where('etat', 'Rejetée')->count())
                ->descriptionColor('danger')
                ->description("Totale des alertes non encore confirmées"),


        ];
    }
}
