<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use App\Events\NewArticlePublished;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected static ?string $title = "Nouvel article";

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id(); // Auth::user()->id() → plus court et recommandé
        return $data;
    }

    protected function afterCreate(): void
    {
        // Si l'article est publié, on déclenche l'événement
        if ($this->record->status) {
            event(new NewArticlePublished($this->record));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // v3 recommande ça
    }
}