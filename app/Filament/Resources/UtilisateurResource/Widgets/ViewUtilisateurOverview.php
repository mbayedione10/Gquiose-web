<?php

namespace App\Filament\Resources\UtilisateurResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Utilisateur;

class ViewUtilisateurOverview extends BaseWidget
{

    public ?Utilisateur $record;

    protected function getStats(): array
    {
        $totalReponses = count($this->record->responses);
        $bonnesReponses = $this->record->responses()->where('isValid', true)->count();

        $mauvaisesReponses = $this->record->responses()->where('isValid', false)->count();

        $tauxDeReussite = $totalReponses != 0 ? ($bonnesReponses * 100) / $totalReponses : 0;

        return [
            Stat::make("Repondues", $totalReponses)
                ->label("Nombre de questions répondues"),

            Stat::make("Bonnes réponses", $bonnesReponses)
                ->description(number_format($tauxDeReussite, 2, ",", " "). "% de taux de reussite")
                ->descriptionColor($tauxDeReussite > 50 ? 'success' : 'danger')
                ->label("Nombre de bonnes réponses"),

            Stat::make("Mauvaises réponses", $mauvaisesReponses)
                ->label("Nombre de mauvaises réponses"),

             Stat::make("Alertes signalées", $this->record->alertes()->count())
                 ->label("Nombre d'alertes signalées")
        ];
    }
}
