
<?php

namespace App\Filament\Resources\EvaluationStatsResource\Widgets;

use App\Models\Evaluation;
use App\Models\QuestionEvaluation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class GlobalStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Évaluations', Evaluation::count())
                ->description('Toutes évaluations confondues')
                ->icon('heroicon-o-clipboard-check')
                ->color('success'),

            Card::make('Score Moyen Global', number_format(Evaluation::avg('score_global') ?? 0, 2) . '/5')
                ->description('Satisfaction générale')
                ->icon('heroicon-o-star')
                ->color('warning'),

            Card::make('Questions Actives', QuestionEvaluation::where('status', true)->count())
                ->description('Questions disponibles')
                ->icon('heroicon-o-question-mark-circle')
                ->color('primary'),

            Card::make('Évaluations (7 jours)', Evaluation::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Dernière semaine')
                ->icon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}
