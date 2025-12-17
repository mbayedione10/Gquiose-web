<?php

namespace App\Filament\Resources\ConseilResource\Pages;

use App\Filament\Resources\ConseilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConseil extends EditRecord
{
    protected static string $resource = ConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
