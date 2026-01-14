<?php

namespace App\Filament\Resources\UtilisateurResource\Pages;

use App\Filament\Resources\UtilisateurResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUtilisateur extends ViewRecord
{
    protected static string $resource = UtilisateurResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            UtilisateurResource\Widgets\ViewUtilisateurOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UtilisateurResource\Widgets\UtilisateurBonneReponseChart::class,
            UtilisateurResource\Widgets\UtilisateurMauvaiseReponseChart::class,
        ];
    }
}
