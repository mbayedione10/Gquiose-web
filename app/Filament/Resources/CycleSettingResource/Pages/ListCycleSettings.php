<?php

namespace App\Filament\Resources\CycleSettingResource\Pages;

use App\Filament\Resources\CycleSettingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCycleSettings extends ListRecords
{
    protected static string $resource = CycleSettingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
