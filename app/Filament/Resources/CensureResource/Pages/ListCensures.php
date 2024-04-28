<?php

namespace App\Filament\Resources\CensureResource\Pages;

use App\Filament\Resources\CensureResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCensures extends ListRecords
{
    protected static string $resource = CensureResource::class;

    protected static ?string $title = "Liste des mot censurés";

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Nouveau mot à censurer")
                ->icon('heroicon-o-plus-circle')
        ];
    }
}
