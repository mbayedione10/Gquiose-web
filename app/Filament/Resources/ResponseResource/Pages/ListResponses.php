<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\ResponseResource;

class ListResponses extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = ResponseResource::class;
}
