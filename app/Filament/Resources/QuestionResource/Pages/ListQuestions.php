<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\QuestionResource\Pages;
use Filament\Pages\Actions\CreateAction;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\QuestionResource;
class ListQuestions extends ListRecords
{
    use HasDescendingOrder;
    protected static string $resource = QuestionResource::class;
    protected static ?string $title = "Liste des questions";
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Nouvelle question")
                ->icon('heroicon-o-plus-circle')
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            QuestionResource\Widgets\QuestionOverview::class,
        ];
    }
}
