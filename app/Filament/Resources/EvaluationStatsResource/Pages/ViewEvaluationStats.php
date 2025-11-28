
<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use App\Filament\Resources\EvaluationStatsResource;
use Filament\Resources\Pages\Page;

class ViewEvaluationStats extends Page
{
    protected static string $resource = EvaluationStatsResource::class;
    protected static string $view = 'filament.resources.evaluation-stats.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\GlobalStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\QuestionChartWidget::class,
        ];
    }
}
