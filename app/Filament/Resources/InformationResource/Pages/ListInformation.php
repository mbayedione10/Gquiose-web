<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\InformationResource\Pages;
use App\Filament\Resources\InformationResource;
use App\Models\Information;
use Filament\Pages\Actions;
class ListInformation extends ListRecords
{
    protected static string $resource = InformationResource::class;
    protected static ?string $title = "Information";
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Nouvelle Information")
                ->visible(Information::count()  == 0)
                ,
        ];
    }
}
