<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\TypeAlerteResource\Pages;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\TypeAlerteResource;
class ListTypeAlertes extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = TypeAlerteResource::class;
}
