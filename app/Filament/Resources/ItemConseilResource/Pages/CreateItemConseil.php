<?php

namespace App\Filament\Resources\ItemConseilResource\Pages;

use App\Filament\Resources\ItemConseilResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItemConseil extends CreateRecord
{
    protected static string $resource = ItemConseilResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
