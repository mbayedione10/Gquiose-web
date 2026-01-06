<?php

namespace App\Filament\Widgets;

use App\Models\Alerte;
use App\Models\Article;
use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Structure;
use App\Models\Utilisateur;
use App\Models\Response;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Calculs pour les utilisateurs
        $totalUtilisateurs = Utilisateur::count();
        $utilisateursActifs = Utilisateur::where('status', true)->count();
        $nouveauxUtilisateurs7j = Utilisateur::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Calculs pour les alertes
        $totalAlertes = Alerte::count();
        $alertesConfirmees = Alerte::where('etat', 'Confirmée')->count();
        $alertes7j = Alerte::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $tauxConfirmation = $totalAlertes > 0 ? round(($alertesConfirmees / $totalAlertes) * 100, 1) : 0;
        
        // Calculs pour le contenu
        $articlesPublies = Article::where('status', true)->count();
        $structuresActives = Structure::where('status', true)->count();
        
        // Calculs pour l'engagement
        $totalEvaluations = Evaluation::count();
        $scoreModyen = Evaluation::avg('score_global') ?? 0;
        $questionsActives = Question::where('status', true)->count();
        $reponsesQuiz = Response::count();
        $bonnesReponses = Response::where('isValid', 1)->count();
        $tauxReussite = $reponsesQuiz > 0 ? round(($bonnesReponses / $reponsesQuiz) * 100, 1) : 0;

        return [
            Stat::make('Utilisateurs Inscrits', number_format($totalUtilisateurs))
                ->description($utilisateursActifs . ' actifs • +' . $nouveauxUtilisateurs7j . ' cette semaine')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getUtilisateursChart()),

            Stat::make('Alertes VBG', number_format($totalAlertes))
                ->description($alertesConfirmees . ' confirmées (' . $tauxConfirmation . '%) • +' . $alertes7j . ' en 7j')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger')
                ->chart($this->getAlertesChart()),

            Stat::make('Contenu Éducatif', number_format($articlesPublies))
                ->description('Articles publiés • ' . $structuresActives . ' structures actives')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info')
                ->chart($this->getArticlesChart()),

            Stat::make('Engagement Quiz', number_format($reponsesQuiz))
                ->description($tauxReussite . '% de réussite • ' . $questionsActives . ' questions actives')
                ->descriptionIcon('heroicon-m-puzzle-piece')
                ->color('warning')
                ->chart($this->getQuizChart()),

            Stat::make('Évaluations App', number_format($totalEvaluations))
                ->description('Note moyenne: ' . number_format($scoreModyen, 1) . '/5 ⭐')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Structures d\'Aide', number_format($structuresActives))
                ->description('Centres de santé et d\'assistance disponibles')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
        ];
    }

    private function getUtilisateursChart(): array
    {
        return collect(range(6, 0))->map(function ($day) {
            return Utilisateur::whereDate('created_at', Carbon::now()->subDays($day))->count();
        })->toArray();
    }

    private function getAlertesChart(): array
    {
        return collect(range(6, 0))->map(function ($day) {
            return Alerte::whereDate('created_at', Carbon::now()->subDays($day))->count();
        })->toArray();
    }

    private function getArticlesChart(): array
    {
        return collect(range(6, 0))->map(function ($day) {
            return Article::whereDate('created_at', Carbon::now()->subDays($day))->count();
        })->toArray();
    }

    private function getQuizChart(): array
    {
        return collect(range(6, 0))->map(function ($day) {
            return Response::whereDate('created_at', Carbon::now()->subDays($day))->count();
        })->toArray();
    }
}