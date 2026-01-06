<?php

namespace App\Filament\Resources\SectionConseilResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SectionConseilResource;

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
