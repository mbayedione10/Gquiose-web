
<?php

namespace App\Filament\Resources\UserNotificationPreferenceResource\Pages;

use App\Filament\Resources\UserNotificationPreferenceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserNotificationPreference extends EditRecord
{
    protected static string $resource = UserNotificationPreferenceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
