<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\LogApiResource\Pages;
use App\Filament\Resources\LogApiResource;
use Filament\Pages\Actions;
class EditLogApi extends EditRecord
{
    protected static string $resource = LogApiResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
