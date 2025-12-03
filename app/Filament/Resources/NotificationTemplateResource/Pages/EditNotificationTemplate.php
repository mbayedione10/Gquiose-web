<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Filament\Resources\NotificationTemplateResource;
use Filament\Pages\Actions;
class EditNotificationTemplate extends EditRecord
{
    protected static string $resource = NotificationTemplateResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
