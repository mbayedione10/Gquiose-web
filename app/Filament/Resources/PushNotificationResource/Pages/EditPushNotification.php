<?php

namespace App\Filament\Resources\PushNotificationResource\Pages;

use App\Filament\Resources\PushNotificationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPushNotification extends EditRecord
{
    protected static string $resource = PushNotificationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
