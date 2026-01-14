<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;

use App\Filament\Resources\NatureContenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNatureContenus extends ListRecords
{
    protected static string $resource = NatureContenuResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
