<?php

namespace App\Filament\Resources\TypeStructureResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\TypeStructureResource;

class ListTypeStructures extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = TypeStructureResource::class;
}
