<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Widgets\QuestionOverview;
use App\Models\Question;
use App\Models\Thematique;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\QuestionResource\Pages;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = "Questions";
    protected static ?string $navigationGroup = "Quiz";
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([

                Select::make('thematique_id')
                    ->rules(['exists:thematiques,id'])
                    ->required()
                    ->relationship('thematique', 'name')
                    ->options(
                        Thematique::whereStatus(true)
                            ->pluck('name', 'id')
                    )
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label("Thêmatique")
                            ->rules(['max:255', 'string'])
                            ->required()
                            ->unique(
                                'thematiques',
                                'name',
                                fn(?Thematique $record) => $record
                            )
                            ->placeholder('Nom de la thématique')
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                        Toggle::make('status')
                            ->label("Activée")
                            ->rules(['boolean'])
                            ->required()
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),
                    ])
                    ->searchable()
                    ->placeholder('Thematique')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('name')
                    ->label("Question")
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->unique(
                        'questions',
                        'name',
                        fn(?Question $record) => $record
                    )
                    ->placeholder('Écrire la question')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('reponse')
                    ->rules(['max:255', 'string'])
                    ->label("Réponse")
                    ->required()
                    ->placeholder('La bonne réponse')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('option1')
                    ->rules(['max:255', 'string'])
                    ->label("Option 1")
                    ->required()
                    ->placeholder('Mauvaise réponse 1')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),


                TextInput::make('option2')
                    ->rules(['max:255', 'string'])
                    ->label("Option 2")
                    ->required()
                    ->placeholder('Mauvaise réponse 2')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('option3')
                    ->rules(['max:255', 'string'])
                    ->label("Option 3")
                    ->nullable()
                    ->placeholder('Mauvaise réponse 3 (optionnelle)')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                TextInput::make('option4')
                    ->rules(['max:255', 'string'])
                    ->label("Option 4")
                    ->nullable()
                    ->placeholder('Mauvaise réponse 4 (optionnelle)')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Toggle::make('status')
                    ->label("Activée")
                    ->rules(['boolean'])
                    ->required()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([

                Tables\Columns\TextColumn::make('thematique.name')
                    ->label("Thématique")
                    ->url(function (?Question  $record){
                        return ThematiqueResource::getUrl('view', ['record' => $record->thematique_id]);
                    })
                    ->limit(50),

                Tables\Columns\TextColumn::make('name')
                    ->label("Question")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('reponse')
                    ->label("Réponse")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('option1')
                    ->label("Option 1")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('option2')
                    ->label("Option 2")
                    ->limit(50),

                Tables\Columns\TextColumn::make('option3')
                    ->label("Option 3")
                    ->limit(50)
                    ->default('-'),

                Tables\Columns\TextColumn::make('option4')
                    ->label("Option 4")
                    ->limit(50)
                    ->default('-'),

                Tables\Columns\ToggleColumn::make('status')
                    ->label("Statut"),


            ]);
    }

    public static function getRelations(): array
    {
        return [
            QuestionResource\RelationManagers\ResponsesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'view' => Pages\ViewQuestion::route('/{record}'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            QuestionOverview::class,
        ];
    }
}
