<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\MenstrualCycleResource\Pages;
use App\Filament\Resources\MenstrualCycleResource;
use Filament\Pages\Actions;
class ListMenstrualCycles extends ListRecords
{
    protected static string $resource = MenstrualCycleResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
