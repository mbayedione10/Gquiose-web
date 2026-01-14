<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Évaluations reçues';

    protected static ?string $navigationGroup = 'Évaluations';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Forms\Components\Placeholder::make('utilisateur_info')
                    ->label('Utilisateur')
                    ->content(fn (Evaluation $record): string => $record->utilisateur ? $record->utilisateur->nom.' '.$record->utilisateur->prenom : 'N/A'),

                Forms\Components\Placeholder::make('contexte_info')
                    ->label('Contexte')
                    ->content(fn (Evaluation $record): string => ucfirst($record->contexte)),

                Forms\Components\Placeholder::make('score_info')
                    ->label('Score Global')
                    ->content(fn (Evaluation $record): string => $record->score_global ? number_format($record->score_global, 2).'/5' : 'N/A'),

                Forms\Components\Placeholder::make('commentaire_info')
                    ->label('Commentaire')
                    ->content(fn (Evaluation $record): string => $record->commentaire ?: 'Aucun commentaire'),

                Forms\Components\Placeholder::make('date_info')
                    ->label('Date de soumission')
                    ->content(fn (Evaluation $record): string => $record->created_at->format('d/m/Y à H:i')),

                Forms\Components\View::make('filament.resources.evaluation.view-reponses')
                    ->label('Réponses détaillées')
                    ->columnSpan(12),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('utilisateur.name')
                    ->label('Utilisateur')
                    ->searchable(['nom', 'prenom']),

                TextColumn::make('contexte')
                    ->label('Contexte')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'structure' => 'Structure',
                        'generale' => 'Générale',
                        'alerte' => 'Alerte',
                        default => $state,
                    }),

                TextColumn::make('score_global')
                    ->label('Score')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1).'/5' : 'N/A')
                    ->sortable(),

                TextColumn::make('commentaire')
                    ->label('Commentaire')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('contexte')
                    ->label('Contexte')
                    ->options([
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'structure' => 'Structure',
                        'generale' => 'Générale',
                        'alerte' => 'Alerte',
                    ]),

                SelectFilter::make('question_evaluation_id')
                    ->label('Question')
                    ->options(fn () => \App\Models\QuestionEvaluation::pluck('question', 'id'))
                    ->searchable()
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            return $query->whereHas('reponsesEvaluations', function ($q) use ($data) {
                                $q->where('question_evaluation_id', $data['value']);
                            });
                        }

                        return $query;
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
        ];
    }
}
