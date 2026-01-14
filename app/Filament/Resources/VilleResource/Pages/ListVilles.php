<?php

namespace App\Filament\Resources\VilleResource\Pages;

use App\Filament\Resources\VilleResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListVilles extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = VilleResource::class;
}
