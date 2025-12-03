<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource;
use Filament\Pages\Actions;
class EditVideo extends EditRecord
{
    protected static string $resource = VideoResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
