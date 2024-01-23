<?php

namespace App\Filament\Resources\LogApiResource\Pages;

use App\Filament\Resources\LogApiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogApis extends ListRecords
{
    protected static string $resource = LogApiResource::class;

    protected static ?string $title = "Monitoring";

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
