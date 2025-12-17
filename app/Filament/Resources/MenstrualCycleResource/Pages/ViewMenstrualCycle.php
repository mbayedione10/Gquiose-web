<?php

namespace App\Filament\Resources\MenstrualCycleResource\Pages;

use App\Filament\Resources\MenstrualCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenstrualCycle extends ViewRecord
{
    protected static string $resource = MenstrualCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
