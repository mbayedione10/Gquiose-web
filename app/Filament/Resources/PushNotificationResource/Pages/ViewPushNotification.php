<?php

use Filament\Resources\Pages\ViewRecord;
<?php

namespace App\Filament\Resources\PushNotificationResource\Pages;
use App\Filament\Resources\PushNotificationResource;
use Filament\Pages\Actions;
class ViewPushNotification extends ViewRecord
{
    protected static string $resource = PushNotificationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            PushNotificationResource\Widgets\NotificationStatsWidget::class,
        ];
    }
}
