<?php

namespace App\Filament\Resources\SectionConseilResource\Pages;

use App\Filament\Resources\SectionConseilResource;
use Filament\Resources\Pages\EditRecord;

class EditSectionConseil extends EditRecord
{
    protected static string $resource = SectionConseilResource::class;

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
