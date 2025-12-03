<?php

namespace App\Filament\Resources\UserNotificationPreferenceResource\Pages;

use App\Filament\Resources\UserNotificationPreferenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserNotificationPreferences extends ListRecords
{
    protected static string $resource = UserNotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}