<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\NatureContenuResource\Pages;
use App\Filament\Resources\NatureContenuResource;
use Filament\Pages\Actions\CreateAction;
class ListNatureContenus extends ListRecords
{
    protected static string $resource = NatureContenuResource::class;
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
