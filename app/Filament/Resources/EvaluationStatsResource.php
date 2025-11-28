<?php

namespace App\Filament\Resources;

use App\Models\QuestionEvaluation;
use App\Models\ReponseEvaluation;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\EvaluationStatsResource\Pages;

class EvaluationStatsResource extends Resource
{
    protected static ?string $model = QuestionEvaluation::class;
    protected static ?string $navigationLabel = 'Statistiques & Graphiques';
    protected static ?string $navigationGroup = 'Évaluations';
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?int $navigationSort = 32;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('formulaire_type')
                    ->label('Type de formulaire')
                    ->enum([
                        'generale' => 'Générale',
                        'satisfaction_quiz' => 'Quiz',
                        'satisfaction_article' => 'Article',
                        'satisfaction_structure' => 'Structure',
                    ])
                    ->sortable(),

                TextColumn::make('question')
                    ->label('Question')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->enum([
                        'text' => 'Texte',
                        'rating' => 'Étoiles',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiples',
                        'scale' => 'Échelle',
                    ]),

                TextColumn::make('reponsesEvaluations_count')
                    ->label('Réponses')
                    ->counts('reponsesEvaluations')
                    ->sortable(),

                TextColumn::make('moyenne')
                    ->label('Moyenne')
                    ->getStateUsing(function ($record) {
                        if (in_array($record->type, ['rating', 'scale'])) {
                            $avg = $record->reponsesEvaluations()->avg('valeur_numerique');
                            return $avg ? number_format($avg, 2) : 'N/A';
                        }
                        return 'N/A';
                    }),
            ])
            ->filters([
                SelectFilter::make('formulaire_type')
                    ->label('Type de formulaire')
                    ->options([
                        'generale' => 'Générale',
                        'satisfaction_quiz' => 'Quiz',
                        'satisfaction_article' => 'Article',
                        'satisfaction_structure' => 'Structure',
                    ]),

                SelectFilter::make('type')
                    ->label('Type de question')
                    ->options([
                        'text' => 'Texte',
                        'rating' => 'Étoiles',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiples',
                        'scale' => 'Échelle',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('voir_stats')
                    ->label('Voir graphique')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn ($record) => route('filament.resources.evaluation-stats.view', $record)),
            ])
            ->defaultSort('reponsesEvaluations_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationStats::route('/'),
            'view' => Pages\ViewEvaluationStats::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
