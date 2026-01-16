<?php

namespace App\Filament\Resources\NotificationLogResource\Pages;

use App\Filament\Resources\NotificationLogResource;
use App\Filament\Resources\NotificationLogResource\Widgets\NotificationLogStatsWidget;
use App\Filament\Resources\NotificationLogResource\Widgets\NotificationsByCategoryWidget;
use App\Filament\Resources\NotificationLogResource\Widgets\NotificationsTrendWidget;
use Filament\Resources\Pages\ListRecords;

class ListNotificationLogs extends ListRecords
{
    protected static string $resource = NotificationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NotificationLogStatsWidget::class,
            NotificationsByCategoryWidget::class,
            NotificationsTrendWidget::class,
        ];
    }
}
