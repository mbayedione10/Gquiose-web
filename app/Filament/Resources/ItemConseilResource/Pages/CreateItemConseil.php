<?php

namespace App\Filament\Resources\ItemConseilResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ItemConseilResource;

class CreateItemConseil extends CreateRecord
{
    protected static string $resource = ItemConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
