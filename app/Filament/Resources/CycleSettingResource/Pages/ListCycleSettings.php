<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\CycleSettingResource\Pages;
use App\Filament\Resources\CycleSettingResource;
use Filament\Pages\Actions;
class ListCycleSettings extends ListRecords
{
    protected static string $resource = CycleSettingResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
