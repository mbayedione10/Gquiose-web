<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CategorieConseilResource;

class ListCategorieConseils extends ListRecords
{
    protected static string $resource = CategorieConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
