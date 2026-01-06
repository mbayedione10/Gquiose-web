<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentArticlesWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Article::query()
            ->with(['rubrique', 'user'])
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('image')
                ->label('Image')
                ->circular()
                ->defaultImageUrl(url('/images/default-article.png')),
            
            Tables\Columns\TextColumn::make('title')
                ->label('Titre')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->limit(60)
                ->description(fn ($record) => str($record->description ?? '')->limit(100)),
            
            Tables\Columns\TextColumn::make('rubrique.name')
                ->label('Rubrique')
                ->badge()
                ->color('primary')
                ->default('Non classé'),
            
            Tables\Columns\IconColumn::make('status')
                ->label('Publié')
                ->boolean()
                ->trueIcon('heroicon-m-check-circle')
                ->falseIcon('heroicon-m-x-circle')
                ->trueColor('success')
                ->falseColor('gray'),
            
            Tables\Columns\TextColumn::make('user.name')
                ->label('Auteur')
                ->icon('heroicon-m-user')
                ->default('Admin'),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('Publié le')
                ->dateTime('d/m/Y')
                ->sortable()
                ->since()
                ->description(fn ($record) => $record->created_at->diffForHumans()),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Articles récents';
    }

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.articles.edit', ['record' => $record]);
    }
}
