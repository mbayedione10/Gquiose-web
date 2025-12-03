<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource;
use Filament\Pages\Actions;
class EditMessage extends EditRecord
{
    protected static string $resource = MessageResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
