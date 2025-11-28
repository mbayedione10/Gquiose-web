<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\NatureContenuResource;
use Filament\Pages\Actions\CreateAction;

class ListNatureContenus extends ListRecords
{
    protected static string $resource = NatureContenuResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
