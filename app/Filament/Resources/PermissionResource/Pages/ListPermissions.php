<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = PermissionResource::class;
}
