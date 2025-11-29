
<?php

namespace App\Filament\Widgets;

use App\Models\Utilisateur;
use App\Models\Alerte;
use App\Models\Article;
use App\Models\Question;
use App\Models\Structure;
use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getCards(): array
    {
        return [
            Card::make('Utilisateurs', Utilisateur::count())
                ->description(Utilisateur::where('status', true)->count() . ' actifs')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, Utilisateur::count()]),

            Card::make('Alertes', Alerte::count())
                ->description(Alerte::where('etat', 'Confirmée')->count() . ' confirmées')
                ->descriptionIcon('heroicon-s-shield-exclamation')
                ->color('danger')
                ->chart([5, 8, 10, 12, 15, 18, Alerte::count()]),

            Card::make('Articles Publiés', Article::where('status', true)->count())
                ->description('Total: ' . Article::count())
                ->descriptionIcon('heroicon-s-document-text')
                ->color('primary')
                ->chart([10, 15, 18, 20, 22, 25, Article::count()]),

            Card::make('Structures d\'Aide', Structure::where('status', true)->count())
                ->description('Centres de santé actifs')
                ->descriptionIcon('heroicon-s-building-office')
                ->color('warning')
                ->chart([8, 10, 12, 14, 15, 16, Structure::count()]),

            Card::make('Questions Quiz', Question::where('status', true)->count())
                ->description('Questions actives')
                ->descriptionIcon('heroicon-s-question-mark-circle')
                ->color('info'),

            Card::make('Évaluations', Evaluation::count())
                ->description('Score moyen: ' . number_format(Evaluation::avg('score_global') ?? 0, 2) . '/5')
                ->descriptionIcon('heroicon-s-star')
                ->color('success'),
        ];
    }
}
