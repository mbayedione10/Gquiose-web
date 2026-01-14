<?php

namespace App\Filament\Resources\UtilisateurResource\Widgets;

use App\Models\Utilisateur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UtilisateurOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total', Utilisateur::count())
                ->description('Utilisateurs ayant crée un compte'),

            Stat::make('Actifs', Utilisateur::whereStatus(true)->count())
                ->description('Utilisateurs ayant confirmé leur email'),

            Stat::make('Inactifs', Utilisateur::whereStatus(false)->count())
                ->description('Utilisateurs ayant pas confirmé leur email'),
        ];
    }
}
