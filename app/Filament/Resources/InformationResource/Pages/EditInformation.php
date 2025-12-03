<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\InformationResource\Pages;
use App\Filament\Resources\InformationResource;
use Filament\Pages\Actions;
class EditInformation extends EditRecord
{
    protected static string $resource = InformationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
