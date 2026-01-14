<?php

namespace App\Filament\Resources\RubriqueResource\Pages;

use App\Filament\Resources\RubriqueResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRubriques extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = RubriqueResource::class;

    protected static ?string $title = 'Liste des rubriques';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle rubrique')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
