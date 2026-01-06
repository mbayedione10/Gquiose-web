<?php

namespace App\Filament\Resources\SectionConseilResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SectionConseilResource;

class CreateSectionConseil extends CreateRecord
{
    protected static string $resource = SectionConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
