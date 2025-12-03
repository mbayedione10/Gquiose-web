<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\LogApiResource\Pages;
use App\Filament\Resources\LogApiResource;
use Filament\Pages\Actions;
class ListLogApis extends ListRecords
{
    protected static string $resource = LogApiResource::class;
    protected static ?string $title = "Monitoring";
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
