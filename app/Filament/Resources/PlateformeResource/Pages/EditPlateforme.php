<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\PlateformeResource\Pages;
use App\Filament\Resources\PlateformeResource;
use Filament\Pages\Actions\DeleteAction;
class EditPlateforme extends EditRecord
{
    protected static string $resource = PlateformeResource::class;
    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
