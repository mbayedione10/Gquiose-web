<?php

namespace App\Filament\Resources\ThematiqueResource\Pages;

use App\Events\NewQuizPublished;
use App\Filament\Resources\ThematiqueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateThematique extends CreateRecord
{
    protected static string $resource = ThematiqueResource::class;

    protected function afterCreate(): void
    {
        // Déclencher la notification push automatique pour le nouveau quiz
        if ($this->record->status) {
            event(new NewQuizPublished($this->record));
        }
    }
}
