<?php

namespace App\Filament\Resources\UserNotificationPreferenceResource\Pages;

use App\Filament\Resources\UserNotificationPreferenceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditUserNotificationPreference extends EditRecord
{
    protected static string $resource = UserNotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}