<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\CycleReminderResource\Pages;
use App\Filament\Resources\CycleReminderResource;
use Filament\Pages\Actions;
class ListCycleReminders extends ListRecords
{
    protected static string $resource = CycleReminderResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
