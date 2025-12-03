<?php

namespace App\Filament\Resources\FaqResource\Pages;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\FaqResource;
use Filament\Pages\Actions;
class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
