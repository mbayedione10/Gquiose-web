<?php

namespace App\Filament\Resources\SectionConseilResource\Pages;

use App\Filament\Resources\SectionConseilResource;
use Filament\Resources\Pages\ListRecords;

class ListSectionConseils extends ListRecords
{
    protected static string $resource = SectionConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
