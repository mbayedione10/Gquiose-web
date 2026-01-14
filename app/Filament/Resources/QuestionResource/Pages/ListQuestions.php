<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = QuestionResource::class;

    protected static ?string $title = 'Liste des questions';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle question')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QuestionResource\Widgets\QuestionOverview::class,
        ];
    }
}
