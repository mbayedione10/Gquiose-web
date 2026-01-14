<?php

namespace App\Filament\Resources\QuestionEvaluationResource\Pages;

use App\Filament\Resources\QuestionEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionEvaluation extends EditRecord
{
    protected static string $resource = QuestionEvaluationResource::class;

    protected static ?string $title = 'Modifier la question';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return QuestionEvaluationResource::getUrl();
    }
}
