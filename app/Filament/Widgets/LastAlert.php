<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LastAlert extends BaseWidget
{

    protected static ?string $heading = '15 dernières alertes';

    protected int | string | array $columnSpan = "full";

    protected static ?int $sort = 15;
    protected function getTableQuery(): Builder
    {
        return  Alerte::query()
            ->with(['utilisateur'])
            ->latest()
            ->limit(15);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ref')
                ->searchable()
                ->limit(50),


            Tables\Columns\TextColumn::make("utilisateur.name")
                ->label("Signalée par")
                ->sortable(),

            Tables\Columns\TextColumn::make("type")
                ->label("Type")
                ->searchable()
                ->sortable(),

            Tables\Columns\BadgeColumn::make('etat')
                ->label("État")
                ->colors([
                    'warning' => static fn ($state): bool => $state === 'Non approuvée',
                    'success' => static fn ($state): bool => $state === 'Confirmée',
                    'danger' => static fn ($state): bool => $state === 'Rejetée',
                ])
                ->searchable()
                ->limit(50),

            Tables\Columns\TextColumn::make('description')
                ->label("Information")
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label("Signalée ")
                ->searchable()
                ->date("d F Y H:i")
                ->limit(50),
        ];
    }
}
