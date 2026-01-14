<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Events\NewVideoPublished;
use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;

    protected function afterCreate(): void
    {
        // Déclencher la notification push automatique
        event(new NewVideoPublished($this->record));
    }
}
