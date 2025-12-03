<?php

namespace App\Filament\Resources\CycleReminderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CycleReminderResource;
use Filament\Actions;

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
