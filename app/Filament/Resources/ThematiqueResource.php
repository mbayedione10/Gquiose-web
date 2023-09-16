<?php

namespace App\Filament\Resources;

use App\Models\Thematique;
use Filament\{Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\ThematiqueResource\Pages;

class ThematiqueResource extends Resource
{
    protected static ?string $model = Thematique::class;


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = "Thématiques";
    protected static ?string $navigationGroup = "Quiz";
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 22;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('name')
                    ->label("Thématique")
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

            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Nom")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label("Questions")
                    ->sortable()
                    ->counts('questions'),

                Tables\Columns\IconColumn::make('status')
                    ->label("Statut")
                    ->boolean(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ThematiqueResource\RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThematiques::route('/'),
            'create' => Pages\CreateThematique::route('/create'),
            'view' => Pages\ViewThematique::route('/{record}'),
            'edit' => Pages\EditThematique::route('/{record}/edit'),
        ];
    }
}
