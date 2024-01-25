<?php

namespace App\Filament\Resources\QuestionResource\Widgets;

use App\Models\Question;
use App\Models\Response;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class QuestionOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $playersToday = Response::select('id', 'utilisateur_id')
            ->groupBy('utilisateur_id')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $responsesToday = Response::select('id')
            ->groupBy('id')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return [
            Card::make('Questions', Question::count())
                ->description("Nombre de questions")
                ->icon("heroicon-o-question-mark-circle"),

            Card::make('Réponses', Response::count())
                ->description("Nombre de réponses")
                ->icon("heroicon-o-question-mark-circle"),


            Card::make('Bonnes réponses', Response::where('isValid', 1)->count())
                ->description("Nombre de bonnes réponses")
                ->icon("heroicon-o-question-mark-circle"),

            Card::make('Mauvaises réponses', Response::where('isValid', 0)->count())
                ->description("Nombre de mauvaises réponses")
                ->icon("heroicon-o-question-mark-circle"),

            Card::make('Joueurs', $playersToday)
                ->description("Nombre de joueurs aujourd'hui")
                ->icon("heroicon-o-users"),

            Card::make('Réponses', $playersToday)
                ->description("Nombre de réponses aujourd'hui")
                ->icon("heroicon-o-question-mark-circle"),


        ];
    }
}
