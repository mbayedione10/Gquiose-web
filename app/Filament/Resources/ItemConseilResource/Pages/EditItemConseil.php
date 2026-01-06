<?php

namespace App\Filament\Resources\ItemConseilResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ItemConseilResource;

class EditItemConseil extends EditRecord
{
    protected static string $resource = ItemConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
