
<?php

namespace App\Filament\Resources\MenstrualCycleResource\Pages;

use App\Filament\Resources\MenstrualCycleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenstrualCycle extends EditRecord
{
    protected static string $resource = MenstrualCycleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
