<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Events\NewArticlePublished;
use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected static ?string $title = 'Nouvel article';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Déclencher la notification push automatique
        if ($this->record->status) {
            event(new NewArticlePublished($this->record));
        }
    }

    protected function getRedirectUrl(): string
    {
        return ArticleResource::getUrl();
    }
}
