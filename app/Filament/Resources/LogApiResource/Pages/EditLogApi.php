<?php

namespace App\Filament\Resources\LogApiResource\Pages;

use App\Filament\Resources\LogApiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogApi extends EditRecord
{
    protected static string $resource = LogApiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
