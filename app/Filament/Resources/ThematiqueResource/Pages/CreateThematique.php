<?php

namespace App\Filament\Resources\ThematiqueResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ThematiqueResource;
use App\Events\NewQuizPublished;

class CreateThematique extends CreateRecord
{
    protected static string $resource = ThematiqueResource::class;

    protected function afterCreate(): void
    {
        // Dispatcher l'event pour envoyer une notification
        NewQuizPublished::dispatch($this->record);
    }
}
