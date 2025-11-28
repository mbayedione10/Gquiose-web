<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use App\Filament\Resources\EvaluationStatsResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluationStats extends ViewRecord
{
    protected static string $resource = EvaluationStatsResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\QuestionChartWidget::class,
        ];
    }

    protected static string $view = 'filament.resources.evaluation-stats.view';
}
