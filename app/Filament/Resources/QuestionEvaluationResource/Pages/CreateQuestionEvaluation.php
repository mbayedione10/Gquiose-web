<?php

namespace App\Filament\Resources\QuestionEvaluationResource\Pages;

use App\Filament\Resources\QuestionEvaluationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestionEvaluation extends CreateRecord
{
    protected static string $resource = QuestionEvaluationResource::class;

    protected static ?string $title = "Nouvelle question d'évaluation";

    protected function getRedirectUrl(): string
    {
        return QuestionEvaluationResource::getUrl();
    }
}
