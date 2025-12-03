<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\CycleSettingResource\Pages;
use App\Filament\Resources\CycleSettingResource;
use Filament\Pages\Actions;
class EditCycleSetting extends EditRecord
{
    protected static string $resource = CycleSettingResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
