<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource;
use App\Filament\Traits\HasDescendingOrder;
class ListRoles extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = RoleResource::class;
}
