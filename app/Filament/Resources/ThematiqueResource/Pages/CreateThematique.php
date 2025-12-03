<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\ThematiqueResource\Pages;
use App\Filament\Resources\ThematiqueResource;
use App\Events\NewQuizPublished;
use Filament\Actions;
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