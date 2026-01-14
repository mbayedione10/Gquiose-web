<?php

namespace App\Filament\Resources\SuiviResource\Pages;

use App\Filament\Resources\SuiviResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Resources\Pages\ListRecords;

class ListSuivis extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = SuiviResource::class;
}
