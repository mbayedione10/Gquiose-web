<?php

namespace App\Filament\Resources\PushNotificationResource\Widgets;

use App\Models\PushNotification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotificationStatsWidget extends BaseWidget
{
    public ?PushNotification $record = null;

    protected function getStats(): array
    {
        $deliveryRate = $this->record->sent_count > 0
            ? round(($this->record->delivered_count / $this->record->sent_count) * 100, 2)
            : 0;

        $openRate = $this->record->delivered_count > 0
            ? round(($this->record->opened_count / $this->record->delivered_count) * 100, 2)
            : 0;

        $clickRate = $this->record->opened_count > 0
            ? round(($this->record->clicked_count / $this->record->opened_count) * 100, 2)
            : 0;

        return [
            Stat::make('Taux de livraison', $deliveryRate.'%')
                ->description($this->record->delivered_count.' / '.$this->record->sent_count.' livrés')
                ->color('success'),

            Stat::make('Taux d\'ouverture', $openRate.'%')
                ->description($this->record->opened_count.' / '.$this->record->delivered_count.' ouverts')
                ->color('primary'),

            Stat::make('Taux de conversion', $clickRate.'%')
                ->description($this->record->clicked_count.' / '.$this->record->opened_count.' cliqués')
                ->color('warning'),
        ];
    }
}
