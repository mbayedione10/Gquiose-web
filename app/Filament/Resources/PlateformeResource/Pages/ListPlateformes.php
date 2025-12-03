<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\PlateformeResource\Pages;
use App\Filament\Resources\PlateformeResource;
use Filament\Pages\Actions\CreateAction;
class ListPlateformes extends ListRecords
{
    protected static string $resource = PlateformeResource::class;
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
