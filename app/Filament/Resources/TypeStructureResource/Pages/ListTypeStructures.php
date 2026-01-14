<?php

namespace App\Filament\Resources\TypeStructureResource\Pages;

use App\Filament\Resources\TypeStructureResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListTypeStructures extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = TypeStructureResource::class;
}
