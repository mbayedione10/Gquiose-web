
<?php

namespace App\Filament\Resources\SousTypeViolenceNumeriqueResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SousTypeViolenceNumeriqueResource;

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
