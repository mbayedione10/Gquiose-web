<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\ResponseResource\Pages;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\ResponseResource;
class ListResponses extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = ResponseResource::class;
}
