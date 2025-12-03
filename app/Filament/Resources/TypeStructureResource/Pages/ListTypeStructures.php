<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\TypeStructureResource\Pages;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\TypeStructureResource;
class ListTypeStructures extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = TypeStructureResource::class;
}
