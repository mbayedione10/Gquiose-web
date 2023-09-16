<?php

namespace App\Filament\Resources\SuiviResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SuiviResource;
use App\Filament\Traits\HasDescendingOrder;

class ListSuivis extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = SuiviResource::class;
}
