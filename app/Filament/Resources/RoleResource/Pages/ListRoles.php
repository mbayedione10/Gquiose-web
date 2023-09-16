<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;

class ListRoles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = RoleResource::class;
}
