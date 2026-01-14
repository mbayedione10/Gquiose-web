<?php

namespace App\Filament\Resources\ItemConseilResource\Pages;

use App\Filament\Resources\ItemConseilResource;
use Filament\Resources\Pages\EditRecord;

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
