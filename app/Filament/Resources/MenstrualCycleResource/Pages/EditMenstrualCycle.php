<?php

namespace App\Filament\Resources\MenstrualCycleResource\Pages;

use App\Filament\Resources\MenstrualCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenstrualCycle extends EditRecord
{
    protected static string $resource = MenstrualCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
