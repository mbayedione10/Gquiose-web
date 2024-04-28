<?php

namespace App\Filament\Resources\CensureResource\Pages;

use App\Filament\Resources\CensureResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCensure extends EditRecord
{
    protected static string $resource = CensureResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
