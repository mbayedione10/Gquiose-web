<?php

namespace App\Filament\Resources\VilleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\VilleResource;
use App\Filament\Traits\HasDescendingOrder;

class ListVilles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = VilleResource::class;
}
