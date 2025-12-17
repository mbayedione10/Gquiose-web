<?php

namespace App\Filament\Resources\EvaluationStatsResource\Widgets;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Évaluations', Evaluation::count())
                ->description('Toutes évaluations confondues')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success'),

            Stat::make('Score Moyen Global', number_format(Evaluation::avg('score_global') ?? 0, 2) . '/5')
                ->description('Satisfaction générale')
                ->icon('heroicon-o-star')
                ->color('warning'),

            Stat::make('Questions Actives', QuestionEvaluation::where('status', true)->count())
                ->description('Evaluations disponibles')
                ->icon('heroicon-o-question-mark-circle')
                ->color('primary'),

            Stat::make('Évaluations (7 jours)', Evaluation::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Dernière semaine')
                ->icon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}
