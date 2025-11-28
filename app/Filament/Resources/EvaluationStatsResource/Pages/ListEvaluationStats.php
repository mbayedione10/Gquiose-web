
<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use App\Filament\Resources\EvaluationStatsResource;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationStats extends ListRecords
{
    protected static string $resource = EvaluationStatsResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\GlobalStatsWidget::class,
        ];
    }
}
