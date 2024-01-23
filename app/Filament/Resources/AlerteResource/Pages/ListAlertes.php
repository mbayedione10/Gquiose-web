<?php

namespace App\Filament\Resources\AlerteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AlerteResource;
use App\Filament\Traits\HasDescendingOrder;

class ListAlertes extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = AlerteResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            AlerteResource\Widgets\AlertOverview::class
        ];
    }
}
