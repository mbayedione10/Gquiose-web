<?php

namespace App\Filament\Resources\StructureResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\StructureResource;

class ListStructures extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = StructureResource::class;
}
