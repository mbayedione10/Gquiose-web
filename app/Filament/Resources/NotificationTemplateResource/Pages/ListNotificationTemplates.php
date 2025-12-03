<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Filament\Resources\NotificationTemplateResource;
use Filament\Pages\Actions;
class ListNotificationTemplates extends ListRecords
{
    protected static string $resource = NotificationTemplateResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
