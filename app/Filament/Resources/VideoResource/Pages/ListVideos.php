<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource;
use Filament\Pages\Actions;
class ListVideos extends ListRecords
{
    protected static string $resource = VideoResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
