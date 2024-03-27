<?php

namespace App\Filament\Resources\ThemeResource\Pages;

use App\Filament\Resources\ThemeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThemes extends ListRecords
{
    protected static string $resource = ThemeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
