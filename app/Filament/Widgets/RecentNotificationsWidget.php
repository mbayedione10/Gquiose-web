<?php

namespace App\Filament\Widgets;

use App\Models\PushNotification;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentNotificationsWidget extends BaseWidget
{
    protected static ?int $sort = 12;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return PushNotification::query()
            ->latest()
            ->limit(8);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->limit(50),
            Tables\Columns\TextColumn::make('message')
                ->label('Message')
                ->searchable()
                ->limit(80)
                ->wrap(),
            Tables\Columns\TextColumn::make('icon')
                ->label('Type')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'bell' => 'info',
                    'warning' => 'warning',
                    'success' => 'success',
                    'alert' => 'danger',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Envoyée le')
                ->dateTime('d/m/Y à H:i')
                ->sortable()
                ->since()
                ->description(fn ($record) => $record->created_at->diffForHumans()),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Notifications push envoyées';
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.push-notifications.edit', ['record' => $record]);
    }
}
