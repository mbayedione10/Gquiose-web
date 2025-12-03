<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\PermissionResource;
class ListPermissions extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = PermissionResource::class;
}
