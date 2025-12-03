
<?php

namespace App\Filament\Resources\CycleReminderResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CycleReminderResource;
use Filament\Actions;

class EditCycleReminder extends EditRecord
{
    protected static string $resource = CycleReminderResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
