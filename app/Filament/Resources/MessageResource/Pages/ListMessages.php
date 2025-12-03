<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource;
use Filament\Pages\Actions;
class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
