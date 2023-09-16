<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\ArticleResource;

class ListArticles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = ArticleResource::class;

    protected static ?string $title = "Liste des articles";

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->label("Nouvel article")
                ->icon('heroicon-o-plus-circle')
        ];
    }
}
