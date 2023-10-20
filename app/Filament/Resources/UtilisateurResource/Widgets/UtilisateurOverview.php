<?php

namespace App\Filament\Resources\UtilisateurResource\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UtilisateurOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make("Total", Utilisateur::count())
                ->description("Utilisateurs ayant crée un compte"),

            Card::make("Actifs", Utilisateur::whereStatus(true)->count())
                ->description("Utilisateurs ayant confirmé leur email"),

            Card::make("Inactifs", Utilisateur::whereStatus(false)->count())
                ->description("Utilisateurs ayant pas confirmé leur email")
        ];
    }
}
