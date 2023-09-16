<?php

namespace App\Filament\Resources\RubriqueResource\Pages;

use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\RubriqueResource;

class ListRubriques extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = RubriqueResource::class;

    protected static ?string $title = "Liste des rubriques";

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->label("Nouvelle rubrique")
                ->icon('heroicon-o-plus-circle')
        ];
    }
}
