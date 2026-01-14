<?php

namespace App\Filament\Resources\SousTypeViolenceNumeriqueResource\Pages;

use App\Filament\Resources\SousTypeViolenceNumeriqueResource;
use Filament\Resources\Pages\EditRecord;

class EditSousTypeViolenceNumerique extends EditRecord
{
    protected static string $resource = SousTypeViolenceNumeriqueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
