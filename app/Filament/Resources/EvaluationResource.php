<?php

namespace App\Filament\Resources;

use App\Models\Evaluation;
use App\Filament\Resources\EvaluationResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\View as FormView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $navigationLabel = 'Évaluations reçues';
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

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Placeholder::make('utilisateur_info')
                    ->label('Utilisateur')
                    ->content(fn (Evaluation $record): string => $record->utilisateur?->nom . ' ' . $record->utilisateur?->prenom ?? 'N/A'),

                Placeholder::make('contexte_info')
                    ->label('Contexte')
                    ->content(fn (Evaluation $record): string => ucfirst($record->contexte)),

                Placeholder::make('score_info')
                    ->label('Score Global')
                    ->content(fn (Evaluation $record): string => $record->score_global ? number_format($record->score_global, 2) . '/5' : 'N/A'),

                Placeholder::make('commentaire_info')
                    ->label('Commentaire')
                    ->content(fn (Evaluation $record): string => $record->commentaire ?: 'Aucun commentaire'),

                Placeholder::make('date_info')
                    ->label('Date de soumission')
                    ->content(fn (Evaluation $record): string => $record->created_at->format('d/m/Y à H:i')),

                FormView::make('filament.resources.evaluation.view-reponses')
                    ->label('Réponses détaillées')
                    ->columnSpanFull(),
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
                    ->searchable(['nom', 'prenom'])
                    ->sortable(),

                BadgeColumn::make('contexte')
                    ->label('Contexte')
                    ->enum([
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'structure' => 'Structure',
                        'generale' => 'Générale',
                        'alerte' => 'Alerte',
                    ])
                    ->color(fn (string $state): string => match ($state) {
                        'quiz' => 'success',
                        'article' => 'info',
                        'structure' => 'warning',
                        'generale' => 'primary',
                        'alerte' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('score_global')
                    ->label('Score')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : 'N/A')
                    ->sortable(),

                TextColumn::make('commentaire')
                    ->label('Commentaire')
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState() ?: null),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
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
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'view'  => Pages\ViewEvaluation::route('/{record}'),
        ];
    }
}