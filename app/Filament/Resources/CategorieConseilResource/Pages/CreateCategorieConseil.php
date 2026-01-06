<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CategorieConseilResource;

class CreateCategorieConseil extends CreateRecord
{
    protected static string $resource = CategorieConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
