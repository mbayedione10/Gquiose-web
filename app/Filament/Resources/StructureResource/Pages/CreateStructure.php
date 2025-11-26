<?php

namespace App\Filament\Resources\StructureResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StructureResource;
use App\Events\NewHealthCenterAdded;

class CreateStructure extends CreateRecord
{
    protected static string $resource = StructureResource::class;

    protected function afterCreate(): void
    {
        // Dispatcher l'event pour envoyer une notification
        NewHealthCenterAdded::dispatch($this->record);
    }
}
