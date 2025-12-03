<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\ThemeResource\Pages;
use App\Filament\Resources\ThemeResource;
use Filament\Pages\Actions;
class CreateTheme extends CreateRecord
{
    protected static string $resource = ThemeResource::class;
}
