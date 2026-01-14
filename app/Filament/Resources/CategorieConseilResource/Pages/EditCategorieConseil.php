<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use App\Filament\Resources\CategorieConseilResource;
use Filament\Resources\Pages\EditRecord;

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
