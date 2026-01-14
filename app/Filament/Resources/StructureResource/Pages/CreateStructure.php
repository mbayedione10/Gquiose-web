<?php

namespace App\Filament\Resources\StructureResource\Pages;

use App\Events\NewHealthCenterAdded;
use App\Filament\Resources\StructureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStructure extends CreateRecord
{
    protected static string $resource = StructureResource::class;

    protected function afterCreate(): void
    {
        // Déclencher la notification push automatique
        if ($this->record->status) {
            event(new NewHealthCenterAdded($this->record));
        }
    }
}
