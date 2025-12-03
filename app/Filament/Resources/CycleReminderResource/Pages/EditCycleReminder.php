<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\CycleReminderResource\Pages;
use App\Filament\Resources\CycleReminderResource;
use Filament\Pages\Actions;
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
