<?php

namespace App\Filament\Resources\PlateformeResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PlateformeResource;
use Filament\Pages\Actions\CreateAction;

class ListPlateformes extends ListRecords
{
    protected static string $resource = PlateformeResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
