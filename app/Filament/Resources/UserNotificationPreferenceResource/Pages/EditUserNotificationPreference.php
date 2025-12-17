<?php

namespace App\Filament\Resources\UserNotificationPreferenceResource\Pages;

use App\Filament\Resources\UserNotificationPreferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserNotificationPreference extends EditRecord
{
    protected static string $resource = UserNotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
