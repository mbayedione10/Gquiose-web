<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = ArticleResource::class;

    protected static ?string $title = 'Liste des articles';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvel article')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
