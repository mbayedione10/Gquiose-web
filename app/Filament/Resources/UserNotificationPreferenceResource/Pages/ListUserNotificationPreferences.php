
<?php

namespace App\Filament\Resources\UserNotificationPreferenceResource\Pages;

use App\Filament\Resources\UserNotificationPreferenceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserNotificationPreferences extends ListRecords
{
    protected static string $resource = UserNotificationPreferenceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
