<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Models\QuestionEvaluation;
use Filament\{Tables, Forms};
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use App\Filament\Resources\QuestionEvaluationResource\Pages;
class QuestionEvaluationResource extends Resource
{
    protected static ?string $model = QuestionEvaluation::class;
    protected static ?string $recordTitleAttribute = 'question';
    protected static ?string $navigationLabel = "Questions d'évaluation";
    protected static ?string $navigationGroup = "Évaluations";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 30;
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('question')
                    ->label("Question")
                    ->rules(['required', 'max:500', 'string'])
                    ->required()
                    ->placeholder('Ex: Comment évaluez-vous cette fonctionnalité ?')
                    ->columnSpan(12),
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
                    ->reactive()
                    ->columnSpan(6),
                TextInput::make('ordre')
                    ->label("Ordre d'affichage")
                    ->numeric()
                    ->default(0)
                    ->columnSpan(3),
                Toggle::make('obligatoire')
                    ->label("Question obligatoire")
                    ->default(false)
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
                    ->columnSpan(12),
                Toggle::make('status')
                    ->label("Active")
                    ->default(true)
                    ->columnSpan(12),
            ])->columns(12),
        ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->enum([
                        'text' => 'Texte',
                        'rating' => 'Note',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiple',
                        'scale' => 'Échelle',
                    ]),
                Tables\Columns\TextColumn::make('ordre')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\IconColumn::make('obligatoire')
                    ->label('Obligatoire')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('ordre')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Texte libre',
                        'rating' => 'Note',
                        'yesno' => 'Oui/Non',
                        'multiple_choice' => 'Choix multiples',
                        'scale' => 'Échelle',
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active'),
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
