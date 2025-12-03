<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\NotificationLogResource\Pages;
use App\Filament\Resources\NotificationLogResource;
use Filament\Pages\Actions;
class ListNotificationLogs extends ListRecords
{
    protected static string $resource = NotificationLogResource::class;
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
