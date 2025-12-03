<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\SuiviResource\Pages;
use App\Filament\Resources\SuiviResource;
use App\Filament\Traits\HasDescendingOrder;
class ListSuivis extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = SuiviResource::class;
}
