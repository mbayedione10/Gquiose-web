
<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\NatureContenuResource;
use Filament\Pages\Actions\DeleteAction;

class EditNatureContenu extends EditRecord
{
    protected static string $resource = NatureContenuResource::class;

    protected function getActions(): array
    {
        return [DeleteAction::make()];
    }
}
