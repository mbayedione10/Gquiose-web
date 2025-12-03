<?php

namespace App\Filament\Resources\FaqResource\Pages;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FaqResource;
use Filament\Pages\Actions;
class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;
}
