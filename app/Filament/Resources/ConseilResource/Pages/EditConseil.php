<?php

namespace App\Filament\Resources\ConseilResource\Pages;

use App\Filament\Resources\ConseilResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConseil extends EditRecord
{
    protected static string $resource = ConseilResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
