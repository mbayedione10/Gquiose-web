<?php

namespace App\Filament\Resources\ResponseResource\Pages;

use App\Filament\Resources\ResponseResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListResponses extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = ResponseResource::class;
}
