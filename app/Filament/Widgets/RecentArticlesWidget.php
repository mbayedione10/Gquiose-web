<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentArticlesWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Article::query()
            ->with(['rubrique'])
            ->latest()
            ->limit(15);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->searchable()
                ->sortable()
                ->limit(50),
            Tables\Columns\BadgeColumn::make('rubrique.nom')
                ->label('Rubrique')
                ->color('primary')
                ->default('Non classé')
                ->formatStateUsing(fn ($state) => $state ?? 'Non classé'),
            Tables\Columns\IconColumn::make('status')
                ->label('Statut')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Publié le')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }

    protected function getTableHeading(): string
    {
        return '15 derniers articles';
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.resources.articles.edit', ['record' => $record]);
    }
}
