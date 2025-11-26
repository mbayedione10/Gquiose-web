<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Events\NewVideoPublished;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;

    protected function afterCreate(): void
    {
        // Dispatcher l'event pour envoyer une notification
        NewVideoPublished::dispatch($this->record);
    }
}
