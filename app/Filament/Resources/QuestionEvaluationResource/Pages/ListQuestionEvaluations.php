<?php

namespace App\Filament\Resources\QuestionEvaluationResource\Pages;

use App\Filament\Resources\QuestionEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionEvaluations extends ListRecords
{
    protected static string $resource = QuestionEvaluationResource::class;

    protected static ?string $title = "Questions d'évaluation";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle question'),
        ];
    }
}
