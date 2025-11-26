<?php

namespace App\Filament\Resources\PushNotificationResource\Pages;

use App\Filament\Resources\PushNotificationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPushNotifications extends ListRecords
{
    protected static string $resource = PushNotificationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
