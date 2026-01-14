<?php

namespace App\Filament\Resources\SectionConseilResource\Pages;

use App\Filament\Resources\SectionConseilResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSectionConseil extends CreateRecord
{
    protected static string $resource = SectionConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
