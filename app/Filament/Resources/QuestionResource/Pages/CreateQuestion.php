<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuestionResource;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected static ?string $title = "Nouvelle question";

    protected function getRedirectUrl(): string
    {
        return ArticleResource::getUrl();
    }
}
