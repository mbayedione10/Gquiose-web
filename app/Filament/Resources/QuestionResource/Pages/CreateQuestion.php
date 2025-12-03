<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\QuestionResource;
class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;
    protected static ?string $title = "Nouvelle question";
    protected function getRedirectUrl(): string
    {
        return QuestionResource::getUrl();
    }
}
