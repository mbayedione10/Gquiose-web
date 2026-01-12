<?php

namespace App\Filament\Resources;

use App\Models\QuestionEvaluation;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\QuestionEvaluationResource\Pages;

class QuestionEvaluationResource extends Resource
{
    protected static ?string $model = QuestionEvaluation::class;

    protected static ?string $recordTitleAttribute = 'question';
    protected static ?string $navigationLabel = "Questions d'évaluation";
    protected static ?string $navigationGroup = "Évaluations";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informations de la question')->schema([
                TextInput::make('question')
                    ->label("Question")
                    ->rules(['required', 'max:500', 'string'])
                    ->required()
                    ->placeholder('Ex: Comment évaluez-vous cette fonctionnalité ?')
                    ->columnSpan(12),

                Select::make('formulaire_type')
                    ->label("Type de formulaire")
                    ->options([
                        'generale' => 'Évaluation générale',
                        'satisfaction_quiz' => 'Satisfaction Quiz',
                        'satisfaction_article' => 'Satisfaction Article',
                        'satisfaction_structure' => 'Satisfaction Structure',
                        'satisfaction_alerte' => 'Satisfaction Alerte',
                    ])
                    ->required()
                    ->default('generale')
                    ->columnSpan(6),

                Select::make('type')
                    ->label("Type de question")
                    ->options([
                        'text' => 'Texte libre',
                        'rating' => 'Note (étoiles)',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiples',
                        'scale' => 'Échelle (1-5)',
                    ])
                    ->required()
                    ->live()
                    ->columnSpan(6),

                TextInput::make('ordre')
                    ->label("Ordre d'affichage")
                    ->numeric()
                    ->default(0)
                    ->columnSpan(6),

                Toggle::make('obligatoire')
                    ->label("Question obligatoire")
                    ->default(false)
                    ->columnSpan(3),

                Toggle::make('status')
                    ->label("Active")
                    ->default(true)
                    ->columnSpan(3),

                Repeater::make('options')
                    ->label("Options de réponse")
                    ->schema([
                        TextInput::make('valeur')
                            ->label('Valeur')
                            ->required(),
                    ])
                    ->visible(fn ($get) => in_array($get('type'), ['multiple_choice']))
                    ->default([])
                    ->addActionLabel('Ajouter une option')
                    ->columnSpan(12),
            ])->columns(12),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('formulaire_type')
                    ->label('Formulaire')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'generale' => 'gray',
                        'satisfaction_quiz' => 'success',
                        'satisfaction_article' => 'info',
                        'satisfaction_structure' => 'warning',
                        'satisfaction_alerte' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'generale' => 'Générale',
                        'satisfaction_quiz' => 'Quiz',
                        'satisfaction_article' => 'Article',
                        'satisfaction_structure' => 'Structure',
                        'satisfaction_alerte' => 'Alerte',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'text' => 'Texte',
                        'rating' => 'Note',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiple',
                        'scale' => 'Échelle',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('ordre')
                    ->label('Ordre')
                    ->sortable(),

                Tables\Columns\IconColumn::make('obligatoire')
                    ->label('Obligatoire')
                    ->boolean(),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('ordre')
            ->filters([
                Tables\Filters\SelectFilter::make('formulaire_type')
                    ->label('Type de formulaire')
                    ->options([
                        'generale' => 'Évaluation générale',
                        'satisfaction_quiz' => 'Satisfaction Quiz',
                        'satisfaction_article' => 'Satisfaction Article',
                        'satisfaction_structure' => 'Satisfaction Structure',
                        'satisfaction_alerte' => 'Satisfaction Alerte',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type de question')
                    ->options([
                        'text' => 'Texte libre',
                        'rating' => 'Note',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiples',
                        'scale' => 'Échelle',
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionEvaluations::route('/'),
            'create' => Pages\CreateQuestionEvaluation::route('/create'),
            'edit' => Pages\EditQuestionEvaluation::route('/{record}/edit'),
        ];
    }
}
