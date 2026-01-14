<?php

namespace App\Filament\Resources\TypeAlerteResource\Pages;

use App\Filament\Resources\TypeAlerteResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListTypeAlertes extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = TypeAlerteResource::class;
}
