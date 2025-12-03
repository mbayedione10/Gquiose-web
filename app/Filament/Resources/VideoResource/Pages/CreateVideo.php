<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource;
use App\Events\NewVideoPublished;
use Filament\Actions;
class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;
    protected function afterCreate(): void
    {
        // Déclencher la notification push automatique
        event(new NewVideoPublished($this->record));
    }
}