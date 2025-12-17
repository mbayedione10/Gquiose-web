<?php

namespace App\Filament\Resources\CycleSettingResource\Pages;

use App\Filament\Resources\CycleSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCycleSetting extends EditRecord
{
    protected static string $resource = CycleSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
