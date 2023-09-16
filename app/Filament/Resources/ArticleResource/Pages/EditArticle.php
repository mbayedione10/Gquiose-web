<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ArticleResource;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;
}
