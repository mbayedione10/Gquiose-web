<?php

namespace App\Filament\Resources\ConseilResource\Pages;

use App\Filament\Resources\ConseilResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConseils extends ListRecords
{
    protected static string $resource = ConseilResource::class;

    protected static ?string $title = 'Liste des conseils';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau conseil')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
