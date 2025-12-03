
<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EvaluationStatsResource;

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
