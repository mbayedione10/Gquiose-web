<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\ThemeResource\Pages;
use App\Filament\Resources\ThemeResource;
use Filament\Pages\Actions;
class ListThemes extends ListRecords
{
    protected static string $resource = ThemeResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
