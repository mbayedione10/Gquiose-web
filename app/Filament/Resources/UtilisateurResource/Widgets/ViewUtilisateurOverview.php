<?php

namespace App\Filament\Resources\UtilisateurResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Utilisateur;

class ViewUtilisateurOverview extends BaseWidget
{

    public ?Utilisateur $record;

    protected function getCards(): array
    {
        return [
            Card::make("Repondues", count($this->record->responses))
                ->label("Nombre de questions répondues"),

            Card::make("Bonnes réponses", $this->record->responses()->where('isValid', true)->count())
                ->label("Nombre de bonnes réponses")
        ];
    }
}
