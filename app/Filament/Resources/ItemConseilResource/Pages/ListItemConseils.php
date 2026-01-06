<?php

namespace App\Filament\Resources\ItemConseilResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ItemConseilResource;

class ListItemConseils extends ListRecords
{
    protected static string $resource = ItemConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
