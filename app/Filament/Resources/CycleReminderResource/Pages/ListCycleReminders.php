<?php

namespace App\Filament\Resources\CycleReminderResource\Pages;

use App\Filament\Resources\CycleReminderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
