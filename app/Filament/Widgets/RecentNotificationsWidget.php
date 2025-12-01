<?php

namespace App\Filament\Widgets;

use App\Models\PushNotification;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentNotificationsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return PushNotification::query()
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->searchable()
                ->sortable()
                ->limit(50),
            Tables\Columns\TextColumn::make('message')
                ->label('Message')
                ->searchable()
                ->limit(60)
                ->wrap(),
            Tables\Columns\BadgeColumn::make('icon')
                ->label('Icone'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Envoyée le')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Notifications récentes';
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.resources.push-notifications.edit', ['record' => $record]);
    }
}
