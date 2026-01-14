<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = RoleResource::class;
}
