
<?php

namespace App\Filament\Resources\SousTypeViolenceNumeriqueResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SousTypeViolenceNumeriqueResource;

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
