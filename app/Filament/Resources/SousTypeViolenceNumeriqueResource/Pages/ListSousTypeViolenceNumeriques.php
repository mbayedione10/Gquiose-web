<?php

namespace App\Filament\Resources\SousTypeViolenceNumeriqueResource\Pages;

use App\Filament\Resources\SousTypeViolenceNumeriqueResource;
use Filament\Resources\Pages\ListRecords;

class ListSousTypeViolenceNumeriques extends ListRecords
{
    protected static string $resource = SousTypeViolenceNumeriqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
