<?php

namespace App\Filament\Resources\UtilisateurResource\Pages;

use App\Filament\Resources\UtilisateurResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListUtilisateurs extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = UtilisateurResource::class;

    protected static ?string $title = 'Liste des utilisateurs';

    protected function getHeaderWidgets(): array
    {
        return [
            UtilisateurResource\Widgets\UtilisateurOverview::class,
        ];
    }
}
