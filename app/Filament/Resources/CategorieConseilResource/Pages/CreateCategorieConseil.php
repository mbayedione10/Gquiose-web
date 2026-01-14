<?php

namespace App\Filament\Resources\CategorieConseilResource\Pages;

use App\Filament\Resources\CategorieConseilResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategorieConseil extends CreateRecord
{
    protected static string $resource = CategorieConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
