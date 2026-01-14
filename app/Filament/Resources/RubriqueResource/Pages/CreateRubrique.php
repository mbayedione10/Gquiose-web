<?php

namespace App\Filament\Resources\RubriqueResource\Pages;

use App\Filament\Resources\RubriqueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRubrique extends CreateRecord
{
    protected static string $resource = RubriqueResource::class;

    protected static ?string $title = 'Nouvelle rubrique';

    protected function getRedirectUrl(): string
    {
        return RubriqueResource::getUrl();
    }
}
