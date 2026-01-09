<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LastAlert extends BaseWidget
{
    protected static ?string $heading = 'Alertes VBG récentes';

    protected int | string | array $columnSpan = "full";

    protected static ?int $sort = 6;
    
    protected function getTableQuery(): Builder
    {
        return Alerte::query()
            ->with(['utilisateur', 'ville', 'typeAlerte'])
            ->latest()
            ->limit(10);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ref')
                ->label('Référence')
                ->searchable()
                ->copyable()
                ->weight('bold')
                ->limit(20),

            Tables\Columns\TextColumn::make('typeAlerte.name')
                ->label('Type de violence')
                ->badge()
                ->color('warning')
                ->default('Non classifié'),

            Tables\Columns\TextColumn::make('etat')
                ->label('Statut')
                ->badge()
                ->colors([
                    'warning' => 'Non approuvée',
                    'success' => 'Confirmée',
                    'danger' => 'Rejetée',
                ])
                ->icons([
                    'heroicon-m-clock' => 'Non approuvée',
                    'heroicon-m-check-circle' => 'Confirmée',
                    'heroicon-m-x-circle' => 'Rejetée',
                ]),

            Tables\Columns\TextColumn::make('ville.name')
                ->label('Ville')
                ->icon('heroicon-m-map-pin')
                ->default('Non spécifiée'),

            Tables\Columns\TextColumn::make('utilisateur.name')
                ->label('Signalée par')
                ->icon('heroicon-m-user')
                ->default('Anonyme'),

            Tables\Columns\IconColumn::make('anonymat_souhaite')
                ->label('Anonymat')
                ->boolean()
                ->trueIcon('heroicon-o-eye-slash')
                ->falseIcon('heroicon-o-eye')
                ->trueColor('warning')
                ->falseColor('success'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Date du signalement')
                ->dateTime('d/m/Y à H:i')
                ->sortable()
                ->since()
                ->description(fn ($record) => $record->created_at->diffForHumans()),
        ];
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.alertes.view', ['record' => $record]);
    }
}
