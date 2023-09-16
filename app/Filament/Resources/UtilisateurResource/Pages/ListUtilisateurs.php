<?php

namespace App\Filament\Resources\UtilisateurResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\UtilisateurResource;

class ListUtilisateurs extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = UtilisateurResource::class;
}
