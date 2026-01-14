<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;

use App\Filament\Resources\NatureContenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNatureContenu extends EditRecord
{
    protected static string $resource = NatureContenuResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
