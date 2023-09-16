<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\PermissionResource;

class ListPermissions extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = PermissionResource::class;
}
