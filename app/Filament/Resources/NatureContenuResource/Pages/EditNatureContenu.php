<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;
use App\Filament\Resources\NatureContenuResource;
use Filament\Pages\Actions\DeleteAction;
class EditNatureContenu extends EditRecord
{
    protected static string $resource = NatureContenuResource::class;
    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
