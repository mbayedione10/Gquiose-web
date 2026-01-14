<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use App\Filament\Resources\CategorieConseilResource;
use Filament\Resources\Pages\ListRecords;

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
