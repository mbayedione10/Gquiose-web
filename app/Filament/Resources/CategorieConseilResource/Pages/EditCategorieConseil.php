<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategorieConseilResource;

class EditCategorieConseil extends EditRecord
{
    protected static string $resource = CategorieConseilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
