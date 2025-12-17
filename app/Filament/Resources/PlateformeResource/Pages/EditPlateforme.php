<?php

namespace App\Filament\Resources\PlateformeResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PlateformeResource;
use Filament\Actions\DeleteAction;

class EditPlateforme extends EditRecord
{
    protected static string $resource = PlateformeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
