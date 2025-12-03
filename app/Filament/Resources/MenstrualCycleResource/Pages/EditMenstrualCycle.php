<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\MenstrualCycleResource\Pages;
use App\Filament\Resources\MenstrualCycleResource;
use Filament\Pages\Actions;
class EditMenstrualCycle extends EditRecord
{
    protected static string $resource = MenstrualCycleResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
