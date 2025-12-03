<?php

namespace App\Filament\Resources\AlerteResource\Widgets;
use App\Models\Alerte;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Console\View\Components\Alert;
class AlertOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make("Alertes", Alerte::count())
                ->description("Totale des alertes signalées"),
            Card::make("Confirmées", Alerte::where('etat', 'Confirmée')->count())
                ->descriptionColor('success')
                ->description("Totale des alertes confirmées"),
            Card::make("Non confirmées", Alerte::where('etat', 'Rejetée')->count())
                ->descriptionColor('danger')
                ->description("Totale des alertes non encore confirmées"),
        ];
    }
}
