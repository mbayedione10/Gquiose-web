<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\NatureContenuResource;
use Filament\Actions\DeleteAction;

class EditNatureContenu extends EditRecord
{
    protected static string $resource = NatureContenuResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
