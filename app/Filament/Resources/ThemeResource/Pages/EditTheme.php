<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\ThemeResource\Pages;
use App\Filament\Resources\ThemeResource;
use Filament\Pages\Actions;
class EditTheme extends EditRecord
{
    protected static string $resource = ThemeResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
